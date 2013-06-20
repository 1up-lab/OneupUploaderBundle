<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use Oneup\UploaderBundle\Uploader\Storage\OrphanageStorageInterface;

class OrphanageStorage extends FilesystemStorage implements OrphanageStorageInterface
{
    protected $storage;
    protected $session;
    protected $config;
    protected $type;

    public function __construct(StorageInterface $storage, SessionInterface $session, $config, $type)
    {
        parent::__construct($config['directory']);

        $this->storage = $storage;
        $this->session = $session;
        $this->config = $config;
        $this->type = $type;
    }

    public function upload(File $file, $name, $path = null)
    {
        if(!$this->session->isStarted())
            throw new \RuntimeException('You need a running session in order to run the Orphanage.');

        return parent::upload($file, $name, $this->getPath());
    }

    public function uploadFiles()
    {
        $filesystem = new Filesystem();

        try {
            $files = $this->getFiles();
            $return = array();

            foreach ($files as $file) {
                $return[] = $this->storage->upload(new File($file->getPathname()), str_replace($this->getFindPath(), '', $file));
            }

            return $return;
        } catch (\Exception $e) {
            return array();
        }
    }

    protected function getFiles()
    {
        $finder = new Finder();
        $finder->in($this->getFindPath())->files();

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
