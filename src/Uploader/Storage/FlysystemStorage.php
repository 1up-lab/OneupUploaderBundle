<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Storage;

// TODO V2
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
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
     * @throws FileExistsException
     * @throws FileNotFoundException
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

            $resultFile = new FlysystemFile($path, 0, 'file', $this->filesystem);

            unlink($file->getPathname());

            return $resultFile;
        }

        if ($file instanceof FlysystemFile && $file->getFilesystem() === $this->filesystem) {
            $file->getFilesystem()->rename($file->getPathname(), $path);

            return new FlysystemFile($path, 0, 'file', $this->filesystem);
        }

        if ($file instanceof FileInterface) {
            $manager = new MountManager([
                'chunks' => $file->getFilesystem(),
                'dest' => $this->filesystem,
            ]);

            $manager->move('chunks://' . $file->getPathname(), 'dest://' . $path);
        }

        return new FlysystemFile($path, 0, 'file', $this->filesystem);
    }
}
