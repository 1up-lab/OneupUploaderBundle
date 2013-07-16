<?php

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\ErrorHandler\ErrorHandlerInterface;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class NoopErrorHandler implements ErrorHandlerInterface
{
    public function addException(AbstractResponse $response, Exception $exception)
    {
        // noop
    }
}
