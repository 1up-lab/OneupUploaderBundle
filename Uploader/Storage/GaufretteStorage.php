<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Gaufrette\Stream\Local as LocalStream;
use Gaufrette\StreamMode;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\MetadataSupporter;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class GaufretteStorage implements StorageInterface, ChunkStorageInterface
{
    protected $filesystem;
    protected $bufferSize;
    protected $unhandledChunk;
    protected $chunkPrefix = 'chunks';

    public function __construct(Filesystem $filesystem, $bufferSize)
    {
        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
    }

    public function upload(FileInterface $file, $name, $path = null)
    {
        $path = is_null($path) ? $name : sprintf('%s/%s', $path, $name);

        if ($file instanceof GaufretteFile) {
            if ($file->getFilesystem() == $this->filesystem) {
                $file->getFilesystem()->rename($file->getKey(), $path);

                return $this->filesystem->get($path);
            }
        }

        $this->stream($file, $path, $name);

        return $this->filesystem->get($path);
    }

    public function clear($maxAge)
    {
        $matches = $this->filesystem->listKeys($this->chunkPrefix);

        $limit = time()+$maxAge;
        $toDelete = array();

        // Collect the directories that are old,
        // this also means the files inside are old
        // but after the files are deleted the dirs
        // would remain
        foreach ($matches['dirs'] as $key) {
            if ($limit < $this->filesystem->mtime($key)) {
                $toDelete[] = $key;
            }
        }
        // The same directory is returned for every file it contains
        array_unique($toDelete);
        foreach ($matches['keys'] as $key) {
            if ($limit < $this->filesystem->mtime($key)) {
                $this->filesystem->delete($key);
            }
        }

        foreach($toDelete as $key) {
            // The filesystem will throw exceptions if
            // a directory is not empty
            try {
                $this->filesystem->delete($key);
            } catch (\Exception $e) {
                //do nothing
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
        return;
    }

    public function assembleChunks($chunks, $removeChunk, $renameChunk)
    {
        // the index is only added to be in sync with the filesystem storage
        $path = $this->chunkPrefix.'/'.$this->unhandledChunk['uuid'].'/';
        $filename = $this->unhandledChunk['index'].'_'.$this->unhandledChunk['original'];

        if (empty($chunks)) {
            $target = $filename;
        } else {
            /*
             * The array only contains items with matching prefix until the filename
             * therefore the order will be decided depending on the filename
             * It is only case-insensitive to be overly-careful.
             */
            sort($chunks, SORT_STRING | SORT_FLAG_CASE);
            $target = pathinfo($chunks[0], PATHINFO_BASENAME);
        }

        $this->stream($this->unhandledChunk['chunk'], $path, $target);

        if ($renameChunk) {
            $name = preg_replace('/^(\d+)_/', '', $target);
            $this->filesystem->rename($path.$target, $path.$name);
            $target = $name;
        }
        $uploaded = $this->filesystem->get($path.$target);

        if (!$renameChunk) {
            return $uploaded;
        }

        return new GaufretteFile($uploaded, $this->filesystem);
    }

    public function cleanup($path)
    {
        $this->filesystem->delete($path);
    }

    public function getChunks($uuid)
    {
        return $this->filesystem->listKeys($this->chunkPrefix.'/'.$uuid)['keys'];
    }

    protected function stream($file, $path, $name)
    {
        if ($this->filesystem->getAdapter() instanceof MetadataSupporter) {
            $this->filesystem->getAdapter()->setMetadata($name, array('contentType' => $file->getMimeType()));
        }

        $path = $path.$name;
        // this is a somehow ugly workaround introduced
        // because the stream-mode is not able to create
        // subdirectories.
        if(!$this->filesystem->has($path))
            $this->filesystem->write($path, '', true);

        $src = new LocalStream($file->getPathname());
        $dst = $this->filesystem->createStream($path);

        $src->open(new StreamMode('rb+'));
        $dst->open(new StreamMode('ab+'));

        while (!$src->eof()) {
            $data = $src->read($this->bufferSize);
            $dst->write($data);
        }

        $dst->close();
        $src->close();
    }

}
