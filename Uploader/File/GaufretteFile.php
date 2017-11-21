<?php

namespace Oneup\UploaderBundle\Uploader\File;

use Gaufrette\Adapter\StreamFactory;
use Gaufrette\File;
use Gaufrette\Adapter\AwsS3;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;

class GaufretteFile extends File implements FileInterface
{
    protected $streamWrapperPrefix;
    protected $mimeType;

    /**
     * @param File $file
     * @param FilesystemInterface|Filesystem $filesystem
     * @param string|null $streamWrapperPrefix
     */
    public function __construct(File $file, $filesystem, $streamWrapperPrefix = null)
    {
        $base = interface_exists('Gaufrette\FilesystemInterface')
            ? 'Gaufrette\FilesystemInterface'
            : 'Gaufrette\Filesystem';

        if (!$filesystem instanceof $base) {
            throw new \InvalidArgumentException(sprintf('Expected an instance of "%s", got "%s".', $base, is_object($filesystem) ? get_class($filesystem) : gettype($filesystem)));
        }

        parent::__construct($file->getKey(), $filesystem);
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    /**
     * Returns the size of the file
     *
     * !! WARNING !!
     * Calling this loads the entire file into memory,
     * unless it is on a stream-capable filesystem.
     * In case of bigger files this could throw exceptions,
     * and will have heavy performance footprint.
     * !! ------- !!
     *
     */
    public function getSize()
    {
        // This can only work on streamable files, so basically local files,
        // still only perform it once even on local files to avoid bothering the filesystem.php g
        if ($this->filesystem->getAdapter() instanceof StreamFactory && !$this->size) {
            if ($this->streamWrapperPrefix) {
                try {
                    $this->setSize(filesize($this->streamWrapperPrefix.$this->getKey()));
                } catch (\Exception $e) {
                    // Fail gracefully if there was a problem with opening the file and
                    // let gaufrette load the file into memory allowing it to throw exceptions
                    // if that doesn't work either.
                    // Not empty to make the scrutiziner happy.
                    return parent::getSize();
                }
            }
        }

        return parent::getSize();
    }

    public function getPathname()
    {
        return $this->getKey();
    }

    public function getPath()
    {
        return pathinfo($this->getKey(), PATHINFO_DIRNAME);
    }

    public function getBasename()
    {
        return pathinfo($this->getKey(), PATHINFO_BASENAME);
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        // This can only work on streamable files, so basically local files,
        // still only perform it once even on local files to avoid bothering the filesystem.
        if ($this->filesystem->getAdapter() instanceof StreamFactory && !$this->mimeType) {
            if ($this->streamWrapperPrefix) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $this->mimeType = finfo_file($finfo, $this->streamWrapperPrefix.$this->getKey());
                finfo_close($finfo);
            }
        } elseif ($this->filesystem->getAdapter() instanceof AwsS3 && !$this->mimeType) {
            $metadata = $this->filesystem->getAdapter()->getMetadata($this->getBasename());
            if (isset($metadata['ContentType'])) {
                $this->mimeType = $metadata['ContentType'];
            }
        }

        return $this->mimeType;
    }

    /**
     * Now that we may be able to get the mime-type the extension
     * COULD be guessed based on that, but it would be even less
     * accurate as mime-types can have multiple extensions
     *
     * @return mixed
     */
    public function getExtension()
    {
        return pathinfo($this->getKey(), PATHINFO_EXTENSION);
    }

    public function getFilesystem()
    {
        return $this->filesystem;
    }

}
