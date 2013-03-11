<?php

namespace Oneup\UploaderBundle\Uploader\Chunk;

interface ChunkManagerInterface
{
    public function warmup();
    public function clear();
}