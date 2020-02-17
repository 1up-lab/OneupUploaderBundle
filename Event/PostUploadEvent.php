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

    /**
     * @var FileInterface|File
     */
    protected $file;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param FileInterface|File $file
     */
    public function __construct($file, ResponseInterface $response, Request $request, string $type, array $config)
    {
        $this->file = $file;
        $this->request = $request;
        $this->response = $response;
        $this->type = $type;
        $this->config = $config;
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
