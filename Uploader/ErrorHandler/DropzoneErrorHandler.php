<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class DropzoneErrorHandler implements ErrorHandlerInterface
{
    public function addException(AbstractResponse $response, Exception $exception): void
    {
        $errors[] = $exception;
        $message = $exception->getMessage();
        // Dropzone wants JSON with error message put into 'error' field.
        // This overwrites the previous error message, so we're only displaying the last one.
        $response['error'] = $message;
    }
}
