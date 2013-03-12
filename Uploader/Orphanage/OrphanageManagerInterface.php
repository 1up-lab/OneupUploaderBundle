<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;

interface OrphanageManagerInterface
{
    public function warmup();
    public function clear();
}