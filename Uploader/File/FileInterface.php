<?php

namespace Oneup\UploaderBundle\Uploader\File;

/**
 * Every function in this interface should be considered unsafe.
 * They are only meant to abstract away some basic file functionality.
 * For safe methods rely on the parent functions.
 *
 * Interface FileInterface
 *
 * @package Oneup\UploaderBundle\Uploader\File
 */
interface FileInterface
{
    /**
     * Returns the size of the file
     *
     * @return int
     */
    public function getSize();

    /**
     * Returns the directory of the file without the filename
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns the guessed mime type of the file
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Returns the filename of the file
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the guessed extension of the file
     *
     * @return mixed
     */
    public function getExtension();
} 