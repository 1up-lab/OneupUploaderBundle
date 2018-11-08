<?php

namespace Oneup\UploaderBundle;

final class UploadEvents
{
    const PRE_UPLOAD = 'oneup_uploader.pre_upload';
    const POST_UPLOAD = 'oneup_uploader.post_upload';
    const POST_PERSIST = 'oneup_uploader.post_persist';
    const POST_CHUNK_UPLOAD = 'oneup_uploader.post_chunk_upload';
    const VALIDATION = 'oneup_uploader.validation';

    public static function preUpload(string $mapping): string
    {
        return self::withMapping(self::PRE_UPLOAD, $mapping);
    }

    public static function postUpload(string $mapping): string
    {
        return self::withMapping(self::POST_UPLOAD, $mapping);
    }

    public static function postPersist(string $mapping): string
    {
        return self::withMapping(self::POST_PERSIST, $mapping);
    }

    public static function postChunkUpload(string $mapping): string
    {
        return self::withMapping(self::POST_CHUNK_UPLOAD, $mapping);
    }

    public static function validation(string $mapping): string
    {
        return self::withMapping(self::VALIDATION, $mapping);
    }

    private static function withMapping(string $event, string $mapping): string
    {
        return "{$event}.{$mapping}";
    }
}
