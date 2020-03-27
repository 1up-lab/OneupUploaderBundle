<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Response;

class FineUploaderResponse extends AbstractResponse
{
    /**
     * @var bool
     */
    protected $success;

    /**
     * @var string|null
     */
    protected $error;

    public function __construct()
    {
        $this->success = true;
        $this->error = null;

        parent::__construct();
    }

    public function assemble(): array
    {
        // explicitly overwrite success and error key
        // as these keys are used internaly by the
        // frontend uploader
        $data = $this->data;
        $data['success'] = $this->success;

        if ($this->success) {
            unset($data['error']);
        }

        if (!$this->success) {
            $data['error'] = $this->error;
        }

        return $data;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = (bool) $success;

        return $this;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function setError(string $msg = null): self
    {
        $this->error = $msg;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
