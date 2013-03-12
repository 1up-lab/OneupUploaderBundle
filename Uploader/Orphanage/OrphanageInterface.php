<?php

namespace Oneup\UploaderBundle\Uploader\Orphanage;

interface OrphanageInterface
{
    public function warmup();
    public function clear();
}