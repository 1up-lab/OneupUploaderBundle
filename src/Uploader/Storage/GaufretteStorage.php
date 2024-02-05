<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

use Gaufrette\Adapter\MetadataSupporter;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Oneup\UploaderBundle\Uploader\Gaufrette\StreamManager;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;

class GaufretteStorage extends StreamManager implements StorageInterface
{
    public function __construct(FilesystemInterface $filesystem, int $bufferSize, protected ?string $streamWrapperPrefix = null)
    {
        $base = interface_exists(FilesystemInterface::class)
            ? FilesystemInterface::class
            : Filesystem::class;

        if (!$filesystem instanceof $base) {
            throw new \InvalidArgumentException(sprintf('Expected an instance of "%s", got "%s".', $base, $filesystem::class));
        }

        $this->filesystem = $filesystem;
        $this->buffersize = $bufferSize;
    }

    /**
     * @param FileInterface|GaufretteFile $file
     *
     * @return FileInterface|GaufretteFile
     */
    public function upload($file, string $name, ?string $path = null)
    {
        $path = null === $path ? $name : sprintf('%s/%s', $path, $name);

        if ($this->filesystem instanceof Filesystem && $this->filesystem->getAdapter() instanceof MetadataSupporter) {
            $this->filesystem->getAdapter()->setMetadata($name, ['contentType' => $file->getMimeType()]);
        }

        if ($file instanceof GaufretteFile) {
            if ($file->getFilesystem() === $this->filesystem) {
                $file->getFilesystem()->rename($file->getKey(), $path);

                return new GaufretteFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
            }
        }

        $this->ensureRemotePathExists($path);
        $dst = $this->filesystem->createStream($path);

        $this->openStream($dst, 'w');
        $this->stream($file, $dst);

        if ($file instanceof GaufretteFile) {
            $file->delete();
        } else {
            $filesystem = new LocalFilesystem();
            $filesystem->remove($file->getPathname());
        }

        return new GaufretteFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
    }
}
