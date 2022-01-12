<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Orphanage;

use Oneup\UploaderBundle\Uploader\Chunk\Storage\GaufretteStorage;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteOrphanageStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class OrphanageManager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $config;

    public function __construct(ContainerInterface $container, array $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    public function has(string $key): bool
    {
        return $this->container->has(sprintf('oneup_uploader.orphanage.%s', $key));
    }

    /**
     * @return object|null
     */
    public function get(string $key)
    {
        return $this->container->get(sprintf('oneup_uploader.orphanage.%s', $key));
    }

    public function clear(): void
    {
        // Really ugly solution to clearing the orphanage on gaufrette
        $class = $this->container->getParameter('oneup_uploader.orphanage.class');

        if (GaufretteOrphanageStorage::class === $class) {
            /** @var GaufretteStorage $chunkStorage */
            $chunkStorage = $this->container->get('oneup_uploader.chunks_storage');
            $chunkStorage->clear($this->config['maxage'], $this->config['directory']);

            return;
        }

        $system = new Filesystem();
        $finder = new Finder();

        try {
            $finder->in($this->config['directory'])->date('<=' . -1 * (int) $this->config['maxage'] . 'seconds')->files();
        } catch (\InvalidArgumentException $e) {
            // the finder will throw an exception of type InvalidArgumentException
            // if the directory he should search in does not exist
            // in that case we don't have anything to clean
            return;
        }

        foreach ($finder as $file) {
            $system->remove((string) $file->getRealPath());
        }

        // Now that the files are cleaned, we check if we need to remove some directories as well
        // We use a new instance of the Finder as it as a state
        $finder = new Finder();
        $finder->in($this->config['directory'])->directories();

        /** @var array<int, \Symfony\Component\Finder\SplFileInfo> $dirArray */
        $dirArray = iterator_to_array($finder, false);
        $size = \count($dirArray);

        // We crawl the array backward as the Finder returns the parent first
        for ($i = $size - 1; $i >= 0; --$i) {
            $dir = $dirArray[$i];

            if (!(new \FilesystemIterator((string) $dir))->valid()) {
                $system->remove((string) $dir);
            }
        }
    }
}
