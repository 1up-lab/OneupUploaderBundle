<?php

namespace Oneup\UploaderBundle\Controller;

interface UploadControllerInterface
{
    public function upload();
    public function delete($uuid = null);
}