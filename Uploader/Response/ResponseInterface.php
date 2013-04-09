<?php

namespace Oneup\UploaderBundle\Uploader\Response;

interface ResponseInterface extends \ArrayAccess
{
    public function assemble();
}