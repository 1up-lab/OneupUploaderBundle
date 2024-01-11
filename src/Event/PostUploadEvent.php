<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Event;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;
use Oneup\UploaderBundle\UploadEvents;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class PostUploadEvent extends Event
{
    public const NAME = UploadEvents::POST_UPLOAD;

    public function __construct(protected FileInterface|File $file, protected ResponseInterface $response, protected Request $request, protected string $type, protected array $config)
    {
    }

    /**
     * @return FileInterface|File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
