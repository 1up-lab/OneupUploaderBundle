<?php

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use Gaufrette\Adapter\StreamFactory;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Oneup\UploaderBundle\Uploader\Gaufrette\StreamManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GaufretteStorage extends StreamManager implements ChunkStorageInterface
{
    protected $unhandledChunk;
    protected $prefix;
    protected $streamWrapperPrefix;

    /**
     * @param FilesystemInterface|Filesystem $filesystem
     * @param int $bufferSize
     * @param string $streamWrapperPrefix
     * @param string $prefix
     */
    public function __construct($filesystem, $bufferSize, $streamWrapperPrefix, $prefix)
    {
        $base = interface_exists('Gaufrette\FilesystemInterface')
            ? 'Gaufrette\FilesystemInterface'
            : 'Gaufrette\Filesystem';

        if (!$filesystem instanceof $base) {
            throw new \InvalidArgumentException(sprintf('Expected an instance of "%s", got "%s".', $base, is_object($filesystem) ? get_class($filesystem) : gettype($filesystem)));
        }

        if (!($filesystem->getAdapter() instanceof StreamFactory)) {
            throw new \InvalidArgumentException('The filesystem used as chunk storage must implement StreamFactory');
        }
        $this->filesystem = $filesystem;
        $this->buffersize = $bufferSize;
        $this->prefix = $prefix;
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    /**
     * Clears files and folders older than $maxAge in $prefix
     * $prefix must be passable so it can clean the orphanage too
     * as it is forced to be the same filesystem.
     *
     * @param      $maxAge
     * @param null $prefix
     */
    public function clear($maxAge, $prefix = null)
    {
        $prefix = $prefix ? :$this->prefix;
        $matches = $this->filesystem->listKeys($prefix);

        $now = time();
        $toDelete = array();

        // Collect the directories that are old,
        // this also means the files inside are old
        // but after the files are deleted the dirs
        // would remain
        foreach ($matches['dirs'] as $key) {
            if ($maxAge <= $now-$this->filesystem->mtime($key)) {
                $toDelete[] = $key;
            }
        }
        // The same directory is returned for every file it contains
        array_unique($toDelete);
        foreach ($matches['keys'] as $key) {
            if ($maxAge <= $now-$this->filesystem->mtime($key)) {
                $this->filesystem->delete($key);
            }
        }

        foreach ($toDelete as $key) {
            // The filesystem will throw exceptions if
            // a directory is not empty
            try {
                $this->filesystem->delete($key);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * Only saves the information about the chunk to avoid moving it
     * forth-and-back to reassemble it. Load distribution is enforced
     * for gaufrette based chunk storage therefore assembleChunks will
     * be called in the same request.
     *
     * @param              $uuid
     * @param              $index
     * @param UploadedFile $chunk
     * @param              $original
     */
    public function addChunk($uuid, $index, UploadedFile $chunk, $original)
    {
        $this->unhandledChunk = array(
            'uuid' => $uuid,
            'index' => $index,
            'chunk' => $chunk,
            'original' => $original
        );
    }

    public function assembleChunks($chunks, $removeChunk, $renameChunk)
    {
        // the index is only added to be in sync with the filesystem storage
        $path = $this->prefix.'/'.$this->unhandledChunk['uuid'].'/';
        $filename = $this->unhandledChunk['index'].'_'.$this->unhandledChunk['original'];

        if (empty($chunks)) {
            $target = $filename;
            $this->ensureRemotePathExists($path.$target);
        } else {
            /*
             * The array only contains items with matching prefix until the filename
             * therefore the order will be decided depending on the filename
             * It is only case-insensitive to be overly-careful.
             */
            sort($chunks, SORT_STRING | SORT_FLAG_CASE);
            $target = pathinfo($chunks[0], PATHINFO_BASENAME);
        }

        $dst = $this->filesystem->createStream($path.$target);
        if ($this->unhandledChunk['index'] === 0) {
            // if it's the first chunk overwrite the already existing part
            // to avoid appending to earlier failed uploads
            $this->openStream($dst, 'w');
        } else {
            $this->openStream($dst, 'a');
        }

        // Meet the interface requirements
        $uploadedFile = new FilesystemFile($this->unhandledChunk['chunk']);

        $this->stream($uploadedFile, $dst);

        if ($renameChunk) {
            $name = preg_replace('/^(\d+)_/', '', $target);
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

        return new GaufretteFile($uploaded, $this->filesystem, $this->streamWrapperPrefix);
    }

    public function cleanup($path)
    {
        $this->filesystem->delete($path);
    }

    public function getChunks($uuid)
    {
        $results = $this->filesystem->listKeys($this->prefix.'/'.$uuid);

        /* exclude files without an index, so if there is a completed file which
         * failed to upload it will not get mixed together with new one's chunks.
         */

        return preg_grep('/^.+\/(\d+)_/', $results['keys']);
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
