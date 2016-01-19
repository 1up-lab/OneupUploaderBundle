<?php
namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use League\Flysystem\Filesystem;
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
        if (
            ! method_exists($filesystem, 'readStream')
            ||
            ! method_exists($filesystem, 'putStream')
        ) {
            throw new \InvalidArgumentException('The filesystem used as chunk storage must streamable');
        }

        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
        $this->prefix = $prefix;
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    public function clear($maxAge, $prefix = null)
    {
        $prefix = $prefix ? :$this->prefix;
        $matches = $this->filesystem->listFiles($prefix);

        $now = time();
        $toDelete = array();

        // Collect the directories that are old,
        // this also means the files inside are old
        // but after the files are deleted the dirs
        // would remain
        foreach ($matches['dirs'] as $key) {
            if ($maxAge <= $now-$this->filesystem->getTimestamp($key)) {
                $toDelete[] = $key;
            }
        }
        // The same directory is returned for every file it contains
        array_unique($toDelete);
        foreach ($matches['keys'] as $key) {
            if ($maxAge <= $now-$this->filesystem->getTimestamp($key)) {
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
        } else {
            sort($chunks, SORT_STRING | SORT_FLAG_CASE);
            $target = pathinfo($chunks[0], PATHINFO_BASENAME);
        }


        if ($this->unhandledChunk['index'] === 0) {
            // if it's the first chunk overwrite the already existing part
            // to avoid appending to earlier failed uploads
            $handle = fopen($path . '/' . $target, 'w');
        } else {
            $handle = fopen($path . '/' . $target, 'a');
        }

        $this->filesystem->putStream($path . $target, $handle);
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

        return new FlysystemFile($uploaded, $this->filesystem, $this->streamWrapperPrefix);
    }

    public function cleanup($path)
    {
        $this->filesystem->delete($path);
    }

    public function getChunks($uuid)
    {
        $results = $this->filesystem->listFiles($this->prefix.'/'.$uuid);
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
