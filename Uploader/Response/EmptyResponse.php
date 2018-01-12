<?php

namespace Oneup\UploaderBundle\Uploader\Response;

class EmptyResponse extends AbstractResponse
{
    public function assemble()
    {
        return $this->data;
    }
}
