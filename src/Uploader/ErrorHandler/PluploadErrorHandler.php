<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class PluploadErrorHandler implements ErrorHandlerInterface
{
    public function addException(AbstractResponse $response, \Exception $exception): void
    {
        /* Plupload only needs an error message so it can be handled client side */
        $message = $exception->getMessage();
        $response['error'] = $message;
    }
}
