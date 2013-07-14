<?php

namespace Oneup\UploaderBundle\Uploader\Exception;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class ValidationException extends UploadException
{
    protected $errorMessage;

    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;

        return $this;
    }

    public function getErrorMessage()
    {
        // if no error message is set, return the exception message
        if (!$this->errorMessage) {
            return $this->getMessage();
        }

        return $this->errorMessage;
    }
}
