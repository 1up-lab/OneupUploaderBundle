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

    public function __construct(protected mixed $chunk, protected ResponseInterface $response, protected Request $request, protected bool $isLast, protected string $type, protected array $config)
    {
    }

    public function getChunk(): mixed
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
