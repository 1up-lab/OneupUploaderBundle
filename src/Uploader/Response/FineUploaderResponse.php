<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Response;

class FineUploaderResponse extends AbstractResponse
{
    public function __construct(protected bool $success = true, protected ?string $error = null)
    {
        parent::__construct();
    }

    public function assemble(): array
    {
        // explicitly overwrite success and error key
        // as these keys are used internally by the
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

    public function setError(?string $msg = null): self
    {
        $this->error = $msg;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
