<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\MountManager;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class FlysystemStorage implements StorageInterface
{
    /**
     * @param FilesystemOperator $filesystem
     * @param int $bufferSize
     * @param string|null $streamWrapperPrefix
     */
    public function __construct(private FilesystemOperator $filesystem, protected int $bufferSize, protected ?string $streamWrapperPrefix = null) {
    }

    /**
     * @param FileInterface|SymfonyFile $file
     *
     * @throws FilesystemException
     *
     * @return FileInterface|SymfonyFile
     */
    public function upload($file, string $name, string $path = null)
    {
        $path = null === $path ? $name : sprintf('%s/%s', $path, $name);

        if ($file instanceof FilesystemFile) {
            /** @var resource $stream */
            $stream = fopen($file->getPathname(), 'r+');

            $this->filesystem->writeStream($path, $stream, [
                'mimetype' => $file->getMimeType(),
            ]);

            if (\is_resource($stream)) {
                fclose($stream);
            }

            $resultFile = new FlysystemFile($path, $this->filesystem);

            unlink($file->getPathname());

            return $resultFile;
        }

        if ($file instanceof FlysystemFile && $file->getFilesystem() === $this->filesystem) {
            $file->getFilesystem()->move($file->getPathname(), $path);

            return new FlysystemFile($path, $this->filesystem);
        }

        if ($file instanceof FileInterface) {
            $manager = new MountManager([
                'chunks' => $file->getFilesystem(),
                'dest' => $this->filesystem,
            ]);

            $manager->move('chunks://' . $file->getPathname(), 'dest://' . $path);
        }

        return new FlysystemFile($path, $this->filesystem);
    }
}
