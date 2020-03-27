<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Event;

use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;
use Oneup\UploaderBundle\UploadEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class PostChunkUploadEvent extends Event
{
    public const NAME = UploadEvents::POST_CHUNK_UPLOAD;

    /**
     * @var mixed
     */
    protected $chunk;

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
     * @var bool
     */
    protected $isLast;

    /**
     * @param mixed $chunk
     */
    public function __construct($chunk, ResponseInterface $response, Request $request, bool $isLast, string $type, array $config)
    {
        $this->chunk = $chunk;
        $this->request = $request;
        $this->response = $response;
        $this->isLast = $isLast;
        $this->type = $type;
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getChunk()
    {
        return $this->chunk;
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

    public function getIsLast(): bool
    {
        return $this->isLast;
    }

    public function isLast(): bool
    {
        return $this->isLast;
    }
}
