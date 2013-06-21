<?php

namespace Oneup\UploaderBundle;

final class UploadEvents
{
    const PRE_UPLOAD        = 'oneup_uploader.pre_upload';
    const POST_UPLOAD       = 'oneup_uploader.post_upload';
    const POST_PERSIST      = 'oneup_uploader.post_persist';
    const POST_CHUNK_UPLOAD = 'oneup_uploader.post_chunk_upload';
    const VALIDATION        = 'oneup_uploader.validation';
}
