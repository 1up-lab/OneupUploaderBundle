<?php

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Oneup\UploaderBundle\Uploader\ErrorHandler\ErrorHandlerInterface;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;

class BlueimpErrorHandler implements ErrorHandlerInterface
{
    public function addException(ResponseInterface $response, UploadException $exception)
    {
        $message = $exception->getErrorMessage();
        $response->addToOffset(array('error' => $message), array('files'));
    }
}
