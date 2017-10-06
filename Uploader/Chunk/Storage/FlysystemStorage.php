<?php

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FlysystemStorage implements ChunkStorageInterface
{
    protected $unhandledChunk;
    protected $prefix;
    protected $streamWrapperPrefix;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public $bufferSize;

    public function __construct(Filesystem $filesystem, $bufferSize, $streamWrapperPrefix, $prefix)
    {
        if (null === $streamWrapperPrefix) {
            throw new \InvalidArgumentException('Stream wrapper must be configured.');
        }

        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
        $this->prefix = $prefix;
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    public function clear($maxAge, $prefix = null)
    {
        $prefix = $prefix ?: $this->prefix;
        $matches = $this->filesystem->listContents($prefix, true);

        $now = time();
        $toDelete = array();

        // Collect the directories that are old,
        // this also means the files inside are old
        // but after the files are deleted the dirs
        // would remain
        foreach ($matches as $key) {
            $path = $key['path'];
            $timestamp = isset($key['timestamp']) ? $key['timestamp'] : $this->filesystem->getTimestamp($path);

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

    public function addChunk($uuid, $index, UploadedFile $chunk, $original)
    {
        $this->unhandledChunk = array(
            'uuid' => $uuid,
            'index' => $index,
            'chunk' => $chunk,
            'original' => $original,
        );
    }

    public function assembleChunks($chunks, $removeChunk, $renameChunk)
    {
        // the index is only added to be in sync with the filesystem storage
        $path = $this->prefix.'/'.$this->unhandledChunk['uuid'].'/';
        $filename = $this->unhandledChunk['index'].'_'.$this->unhandledChunk['original'];

        if (empty($chunks)) {
            $target = $filename;
        } else {
            sort($chunks, SORT_STRING | SORT_FLAG_CASE);
            $target = pathinfo($chunks[0]['path'], PATHINFO_BASENAME);
        }

        $mode = 'ab';
        if (0 === $this->unhandledChunk['index']) {
            // if it's the first chunk overwrite the already existing part
            // to avoid appending to earlier failed uploads
            $mode = 'wb';
        }

        $file = fopen($this->unhandledChunk['chunk']->getPathname(), 'rb');
        $dest = fopen($this->streamWrapperPrefix.'/'.$path.$target, $mode);

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
            if ($this->filesystem->has($path.$name)) {
                $this->filesystem->delete($path.$name);
            }

            $this->filesystem->rename($path.$target, $path.$name);
            $target = $name;
        }
        $uploaded = $this->filesystem->get($path.$target);

        if (!$renameChunk) {
            return $uploaded;
        }

        return new FlysystemFile($uploaded, $this->filesystem, $this->streamWrapperPrefix);
    }

    public function cleanup($path)
    {
        try {
            $this->filesystem->delete($path);
        } catch (FileNotFoundException $e) {
            // File already gone.
        }
    }

    public function getChunks($uuid)
    {
        return $this->filesystem->listFiles($this->prefix.'/'.$uuid);
    }

    public function getFilesystem()
    {
        return $this->filesystem;
    }

    public function getStreamWrapperPrefix()
    {
        return $this->streamWrapperPrefix;
    }
}
