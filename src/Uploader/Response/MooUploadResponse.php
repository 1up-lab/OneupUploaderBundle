<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Response;

class MooUploadResponse extends AbstractResponse
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $error;

    /**
     * @var bool
     */
    protected $finish;

    /**
     * @var string
     */
    protected $uploadedName;

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

    /**
     * @param mixed $id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
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

    /**
     * @param mixed $size
     */
    public function setSize($size): self
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
