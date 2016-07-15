<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\ChunkStorageInterface;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Finder\Finder;

use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use Oneup\UploaderBundle\Uploader\Storage\OrphanageStorageInterface;

class FilesystemOrphanageStorage extends FilesystemStorage implements OrphanageStorageInterface
{
    protected $storage;
    protected $session;
    protected $config;
    protected $type;

    public function __construct(StorageInterface $storage, SessionInterface $session, ChunkStorageInterface $chunkStorage, $config, $type)
    {
        parent::__construct($config['directory']);

        // We can just ignore the chunkstorage here, it's not needed to access the files
        $this->storage = $storage;
        $this->session = $session;
        $this->config = $config;
        $this->type = $type;
    }

    public function upload(FileInterface $file, $name, $path = null)
    {
        if(!$this->session->isStarted())
            throw new \RuntimeException('You need a running session in order to run the Orphanage.');

        return parent::upload($file, $name, $this->getPath());
    }

    public function uploadFiles(array $files = null)
    {
        try {
            if (null === $files) {
                $files = $this->getFiles();
            }
            $return = array();

            foreach ($files as $file) {
                $return[] = $this->storage->upload(new FilesystemFile(new File($file->getPathname())), ltrim(str_replace($this->getFindPath(), '', $file), "/"));
            }

            return $return;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getFiles()
    {
        $finder = new Finder();
        try {
            $finder->in($this->getFindPath())->files();
        } catch (\InvalidArgumentException $e) {
            //catch non-existing directory exception.
            //This can happen if getFiles is called and no file has yet been uploaded

            //push empty array into the finder so we can emulate no files found
            $finder->append(array());
        }

        return $finder;
    }

    protected function getPath()
    {
        return sprintf('%s/%s', $this->session->getId(), $this->type);
    }

    protected function getFindPath()
    {
        return sprintf('%s/%s', $this->config['directory'], $this->getPath());
    }
}
