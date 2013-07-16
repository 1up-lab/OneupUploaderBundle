<?php

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\ErrorHandler\ErrorHandlerInterface;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class BlueimpErrorHandler implements ErrorHandlerInterface
{
    public function addException(AbstractResponse $response, Exception $exception)
    {
        $message = $exception->getMessage();
        $response->addToOffset(array('error' => $message), array('files'));
    }
}
