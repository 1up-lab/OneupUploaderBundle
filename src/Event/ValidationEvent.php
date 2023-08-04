<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Event;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;
use Oneup\UploaderBundle\UploadEvents;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class ValidationEvent extends Event
{
    public const NAME = UploadEvents::VALIDATION;

    public function __construct(protected FileInterface|File $file, protected Request $request, protected array $config, protected string $type, protected ?ResponseInterface $response = null)
    {
    }

    /**
     * @return FileInterface|File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
