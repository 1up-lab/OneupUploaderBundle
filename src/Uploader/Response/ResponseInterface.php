<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Response;

/**
 * @mixin \ArrayAccess
 */
interface ResponseInterface
{
    /**
     * Transforms this object to an array of data.
     *
     * @return array
     */
    public function assemble();
}
