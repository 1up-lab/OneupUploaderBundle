<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Response;

class MooUploadResponse extends AbstractResponse
{
    protected string|int $id;

    protected ?string $name;

    protected int $size;

    protected int $error;

    protected bool $finish;

    protected string $uploadedName;

    public function __construct()
    {
        $this->finish = true;
        $this->error = 0;

        parent::__construct();
    }

    public function assemble(): array
    {
        $data = $this->data;

        $data['id'] = $this->id;
        $data['name'] = $this->name;
        $data['size'] = $this->size;
        $data['error'] = $this->error;
        $data['finish'] = $this->finish;
        $data['upload_name'] = $this->uploadedName;

        return $data;
    }

    public function setId(string|int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function setName(string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSize(string|int $size): self
    {
        $this->size = (int) $size;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setError(int $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function setFinish(bool $finish): self
    {
        $this->finish = $finish;

        return $this;
    }

    public function getFinish(): bool
    {
        return $this->finish;
    }

    public function setUploadedName(string $name): self
    {
        $this->uploadedName = $name;

        return $this;
    }

    public function getUploadedName(): string
    {
        return $this->uploadedName;
    }
}
