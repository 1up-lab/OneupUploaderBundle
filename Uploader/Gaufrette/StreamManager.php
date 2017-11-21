<?php
namespace Oneup\UploaderBundle\Uploader\Gaufrette;

use Gaufrette\Stream;
use Gaufrette\StreamMode;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Gaufrette\Stream\Local as LocalStream;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;

class StreamManager
{
    protected $filesystem;
    public $buffersize;

    protected function createSourceStream(FileInterface $file)
    {
        if ($file instanceof GaufretteFile) {
            // The file is always streamable as the chunk storage only allows
            // adapters that implement StreamFactory
            return $file->createStream();
        }

        return new LocalStream($file->getPathname());
    }

    protected function ensureRemotePathExists($path)
    {
        if(!$this->filesystem->has($path)) {
            $this->filesystem->write($path, '', true);
        }
    }

    protected function openStream(Stream $stream, $mode)
    {
        // always use binary mode
        $mode = $mode.'b+';

        return $stream->open(new StreamMode($mode));
    }

    protected function stream(FileInterface $file, Stream $dst)
    {
        $src = $this->createSourceStream($file);

        // always use reading only for the source
        $this->openStream($src, 'r');

        while (!$src->eof()) {
            $data = $src->read($this->buffersize);
            $dst->write($data);
        }

        $dst->close();
        $src->close();
    }

}
