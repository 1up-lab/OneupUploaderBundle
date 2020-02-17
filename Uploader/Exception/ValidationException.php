<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Exception;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class ValidationException extends UploadException
{
    /**
     * @var string
     */
    protected $errorMessage;

    public function setErrorMessage(string $message): self
    {
        $this->errorMessage = $message;

        return $this;
    }

    public function getErrorMessage(): string
    {
        // if no error message is set, return the exception message
        if (!$this->errorMessage) {
            return $this->getMessage();
        }

        return $this->errorMessage;
    }
}
