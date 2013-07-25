<?php

namespace Oneup\UploaderBundle\Uploader\Chunk;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

use Oneup\UploaderBundle\Uploader\Chunk\ChunkManagerInterface;

class ChunkManager implements ChunkManagerInterface
{
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }

    public function clear()
    {
        $system = new Filesystem();
        $finder = new Finder();

        try {
            $finder->in($this->configuration['directory'])->date('<=' . -1 * (int) $this->configuration['maxage'] . 'seconds')->files();
        } catch (\InvalidArgumentException $e) {
            // the finder will throw an exception of type InvalidArgumentException
            // if the directory he should search in does not exist
            // in that case we don't have anything to clean
            return;
        }

        foreach ($finder as $file) {
            $system->remove($file);
        }
    }

    public function addChunk($uuid, $index, UploadedFile $chunk, $original)
    {
        $filesystem = new Filesystem();
        $path = sprintf('%s/%s', $this->configuration['directory'], $uuid);
        $name = sprintf('%s_%s', $index, $original);

        // create directory if it does not yet exist
        if(!$filesystem->exists($path))
            $filesystem->mkdir(sprintf('%s/%s', $this->configuration['directory'], $uuid));

        return $chunk->move($path, $name);
    }

    public function assembleChunks(\IteratorAggregate $chunks, $removeChunk = true, $renameChunk = false)
    {
        $iterator = $chunks->getIterator()->getInnerIterator();

        $base = $iterator->current();
        $iterator->next();

        while ($iterator->valid()) {

            $file = $iterator->current();

            if (false === file_put_contents($base->getPathname(), file_get_contents($file->getPathname()), \FILE_APPEND | \LOCK_EX)) {
                throw new \RuntimeException('Reassembling chunks failed.');
            }

            if ($removeChunk) {
                $filesystem = new Filesystem();
                $filesystem->remove($file->getPathname());
            }

            $iterator->next();
        }

        $name = $base->getBasename();

        if ($renameChunk) {
            $name = preg_replace('/^(\d+)_/', '', $base->getBasename());
        }

        // remove the prefix added by self::addChunk
        $assembled = new File($base->getRealPath());
        $assembled = $assembled->move($base->getPath(), $name);

        return $assembled;
    }

    public function cleanup($path)
    {
        // cleanup
        $filesystem = new Filesystem();
        $filesystem->remove($path);

        return true;
    }

    public function getChunks($uuid)
    {
        $finder = new Finder();
        $finder
        ->in(sprintf('%s/%s', $this->configuration['directory'], $uuid))->files()->sort(function(\SplFileInfo $a, \SplFileInfo $b) {
            $t = explode('_', $a->getBasename());
            $s = explode('_', $b->getBasename());
            $t = (int) $t[0];
            $s = (int) $s[0];

            return $s < $t;
        });

        return $finder;
    }
    
    public function getLoadDistribution()
    {
        return $this->configuration['load_distribution'];
    }
}
