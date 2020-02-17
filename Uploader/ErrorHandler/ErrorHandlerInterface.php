<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

interface ErrorHandlerInterface
{
    public function addException(AbstractResponse $response, Exception $exception): void;
}
