<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;

class UrlSafeNamer implements NamerInterface
{
    /**
     * Name a given file and return the name.
     *
     * @return string
     */
    public function name(FileInterface $file)
    {
        $bytes = random_bytes(256 / 8);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=') . '.' . $file->getExtension();
    }
}
