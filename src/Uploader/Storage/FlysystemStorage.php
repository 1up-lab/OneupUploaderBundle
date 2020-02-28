<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

use League\Flysystem\File;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class FlysystemStorage implements StorageInterface
{
    /**
     * @var string|null
     */
    protected $streamWrapperPrefix;

    /**
     * @var int
     */
    protected $bufferSize;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(FilesystemInterface $filesystem, int $bufferSize, ?string $streamWrapperPrefix = null)
    {
        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    /**
     * @param FileInterface|SymfonyFile $file
     *
     * @return FileInterface|SymfonyFile
     */
    public function upload($file, string $name, string $path = null)
    {
        $path = null === $path ? $name : sprintf('%s/%s', $path, $name);

        if ($file instanceof FilesystemFile) {
            /** @var resource $stream */
            $stream = fopen($file->getPathname(), 'r+');

            $this->filesystem->putStream($path, $stream, [
                'mimetype' => $file->getMimeType(),
            ]);

            if (\is_resource($stream)) {
                fclose($stream);
            }

            $filesystem = new LocalFilesystem();
            $filesystem->remove($file->getPathname());

            /** @var File $file */
            $file = $this->filesystem->get($path);

            return new FlysystemFile($file, $this->filesystem, $this->streamWrapperPrefix);
        }

        if ($file instanceof FlysystemFile && $file->getFilesystem() === $this->filesystem) {
            $file->getFilesystem()->rename($file->getPath(), $path);

            /** @var File $file */
            $file = $this->filesystem->get($path);

            return new FlysystemFile($file, $this->filesystem, $this->streamWrapperPrefix);
        }

        if ($file instanceof FileInterface) {
            $manager = new MountManager([
                'chunks' => $file->getFilesystem(),
                'dest' => $this->filesystem,
            ]);

            $manager->move(sprintf('chunks://%s', $file->getPathname()), sprintf('dest://%s', $path));
        }

        /** @var File $file */
        $file = $this->filesystem->get($path);

        return new FlysystemFile($file, $this->filesystem, $this->streamWrapperPrefix);
    }
}
