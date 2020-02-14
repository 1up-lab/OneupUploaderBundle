<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Response;

interface ResponseInterface
{
    /**
     * Transforms this object to an array of data.
     *
     * @return array
     */
    public function assemble();
}
