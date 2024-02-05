<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Response;

class MooUploadResponse extends AbstractResponse
{
    protected int|string|null $id;
    protected ?string $name;
    protected int $size;
    protected string $uploadedName;

    public function __construct(protected bool $finish = true, protected int $error = 0)
    {
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

    public function setId(int|string|null $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function setName(?string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSize(mixed $size): self
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
