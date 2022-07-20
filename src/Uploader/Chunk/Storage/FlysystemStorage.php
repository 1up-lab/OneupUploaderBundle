<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FlysystemStorage implements ChunkStorageInterface
{
    /**
     * @var int
     */
    public $bufferSize;

    /**
     * @var array
     */
    protected $unhandledChunk;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $streamWrapperPrefix;

    /**
     * @var FilesystemOperator
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem, int $bufferSize, string $streamWrapperPrefix, string $prefix)
    {
        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
        $this->prefix = $prefix;
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    public function addChunk(string $uuid, int $index, UploadedFile $chunk, string $original): void
    {
        // Prevent path traversal attacks
        $uuid = basename($uuid);

        $this->unhandledChunk = [
            'uuid' => $uuid,
            'index' => $index,
            'chunk' => $chunk,
            'original' => $original,
        ];
    }

    /**
     * @throws FilesystemException
     */
    public function clear(int $maxAge, string $prefix = null): void
    {
        $prefix = $prefix ?: $this->prefix;
        $matches = $this->filesystem->listContents($prefix, true);

        $now = time();
        $toDelete = [];

        // Collect the directories that are old,
        // this also means the files inside are old
        // but after the files are deleted the dirs
        // would remain
        /** @var StorageAttributes $key */
        foreach ($matches as $key) {
            $path = $key->path();
            $timestamp = $key->lastModified();

            if ($maxAge <= $now - $timestamp) {
                $toDelete[] = $path;
            }
        }

        foreach ($toDelete as $path) {
            // The filesystem will throw exceptions if
            // a directory is not empty
            try {
                $this->filesystem->delete($path);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * @param array $chunks
     *
     * @throws FilesystemException
     */
    public function assembleChunks($chunks, bool $removeChunk, bool $renameChunk): FlysystemFile
    {
        // the index is only added to be in sync with the filesystem storage
        $path = $this->prefix . '/' . $this->unhandledChunk['uuid'] . '/';
        $filename = $this->unhandledChunk['index'] . '_' . $this->unhandledChunk['original'];

        if (empty($chunks)) {
            $target = $filename;
        } else {
            sort($chunks, \SORT_STRING | \SORT_FLAG_CASE);
            $target = pathinfo($chunks[0]['path'], \PATHINFO_BASENAME);
        }

        $mode = 'ab';
        if (0 === $this->unhandledChunk['index']) {
            // if it's the first chunk overwrite the already existing part
            // to avoid appending to earlier failed uploads
            $mode = 'wb';
        }

        /** @var resource $file */
        $file = fopen($this->unhandledChunk['chunk']->getPathname(), 'r');

        /** @var resource $dest */
        $dest = fopen($this->streamWrapperPrefix . '/' . $path . $target, $mode);

        stream_copy_to_stream($file, $dest);

        fclose($file);
        fclose($dest);

        if ($renameChunk) {
            $name = $this->unhandledChunk['original'];
            /* The name can only match if the same user in the same session is
             * trying to upload a file under the same name AND the previous upload failed,
             * somewhere between this function, and the cleanup call. If that happened
             * the previous file is unaccessible by the user, but if it is not removed
             * it will block the user from trying to re-upload it.
             */
            if ($this->filesystem->fileExists($path . $name)) {
                $this->filesystem->delete($path . $name);
            }

            $this->filesystem->move($path . $target, $path . $name);
            $target = $name;
        }

        return new FlysystemFile($path . $target, $this->filesystem);
    }

    public function cleanup(string $path): void
    {
        try {
            $this->filesystem->delete($path);
        } catch (FilesystemException $e) {
            // File already gone.
        }
    }

    /**
     * @throws FilesystemException
     */
    public function getChunks(string $uuid): array
    {
        // Prevent path traversal attacks
        $uuid = basename($uuid);

        return $this->filesystem->listContents($this->prefix . '/' . $uuid)
            ->filter(function (StorageAttributes $attributes) { return $attributes->isFile(); })
            ->sortByPath()
            ->map(function (StorageAttributes $attributes) {
                return [
                    'path' => $attributes->path(),
                    'type' => $attributes->type(),
                    'timestamp' => $attributes->lastModified(),
                    'size' => $this->filesystem->fileSize($attributes->path()),
                ];
            })->toArray();
    }

    public function getFilesystem(): FilesystemOperator
    {
        return $this->filesystem;
    }

    public function getStreamWrapperPrefix(): string
    {
        return $this->streamWrapperPrefix;
    }
}
