<?php
    
namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;

interface ErrorHandlerInterface
{
    public function addException(ResponseInterface $response, UploadException $exception);
}
