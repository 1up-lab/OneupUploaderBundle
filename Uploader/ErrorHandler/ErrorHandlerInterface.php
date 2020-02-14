<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

interface ErrorHandlerInterface
{
    /**
     * Adds an exception to a given response.
     */
    public function addException(AbstractResponse $response, Exception $exception);
}
