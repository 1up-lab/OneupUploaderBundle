<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Chunk\Storage;

use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesystemStorage implements ChunkStorageInterface
{
    /**
     * @var string
     */
    protected $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    public function clear(int $maxAge): void
    {
        $system = new Filesystem();
        $finder = new Finder();

        try {
            $finder->in($this->directory)->date('<=' . -1 * (int) $maxAge . 'seconds')->files();
        } catch (\InvalidArgumentException $e) {
            // the finder will throw an exception of type InvalidArgumentException
            // if the directory he should search in does not exist
            // in that case we don't have anything to clean
            return;
        }

        foreach ($finder as $file) {
            $system->remove((string) $file->getRealPath());
        }
    }

    public function addChunk(string $uuid, int $index, UploadedFile $chunk, string $original): File
    {
        // Prevent path traversal attacks
        $uuid = basename($uuid);

        $filesystem = new Filesystem();
        $path = sprintf('%s/%s', $this->directory, $uuid);
        $name = sprintf('%s_%s', $index, $original);

        // create directory if it does not yet exist
        if (!$filesystem->exists($path)) {
            $filesystem->mkdir(sprintf('%s/%s', $this->directory, $uuid));
        }

        return $chunk->move($path, $name);
    }

    /**
     * @param \IteratorAggregate $chunks
     */
    public function assembleChunks($chunks, bool $removeChunk, bool $renameChunk): File
    {
        if (!($chunks instanceof \IteratorAggregate)) {
            throw new \InvalidArgumentException('The first argument must implement \IteratorAggregate interface.');
        }

        /** @var \Iterator $iterator */
        $iterator = $chunks->getIterator();

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
            // remove the prefix added by self::addChunk
            $name = preg_replace('/^(\d+)_/', '', $base->getBasename());
        }

        $assembled = new File($base->getRealPath());
        $assembled = $assembled->move($base->getPath(), $name);

        // the file is only renamed before it is uploaded
        if ($renameChunk) {
            // create an file to meet interface restrictions
            $file = new UploadedFile($assembled->getPathname(), $assembled->getBasename(), null, null, true);
            $assembled = new FilesystemFile($file);
        }

        return $assembled;
    }

    public function cleanup(string $path): void
    {
        // cleanup
        $filesystem = new Filesystem();
        $filesystem->remove($path);
    }

    public function getChunks(string $uuid): Finder
    {
        // Prevent path traversal attacks
        $uuid = basename($uuid);

        $finder = new Finder();
        $finder
            ->in(sprintf('%s/%s', $this->directory, $uuid))->files()->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
                $t = explode('_', $a->getBasename());
                $s = explode('_', $b->getBasename());
                $t = (int) $t[0];
                $s = (int) $s[0];

                return $s < $t;
            });

        return $finder;
    }
}
