<?php

namespace Oneup\UploaderBundle\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class EmptyResponse extends AbstractResponse
{
    public function assemble()
    {
        return $this->data;
    }
}
