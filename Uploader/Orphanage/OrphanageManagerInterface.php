<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;

use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageInterface;

interface OrphanageManagerInterface
{
    public function warmup();
    public function clear();
    public function getImplementation($type);
    public function addImplementation($type, OrphanageInterface $orphanage);
}