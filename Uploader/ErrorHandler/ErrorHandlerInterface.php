<?php

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

interface ErrorHandlerInterface
{
    /**
     * Adds an exception to a given response.
     *
     * @param AbstractResponse $response
     * @param Exception        $exception
     */
    public function addException(AbstractResponse $response, Exception $exception);
}
