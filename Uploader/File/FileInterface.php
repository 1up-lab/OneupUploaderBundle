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
     * Returns the path of the file
     *
     * @return string
     */
    public function getPathname();

    /**
     * Return the path of the file without the filename
     *
     * @return mixed
     */
    public function getPath();

    /**
     * Returns the guessed mime type of the file
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Returns the basename of the file
     *
     * @return string
     */
    public function getBasename();

    /**
     * Returns the guessed extension of the file
     *
     * @return mixed
     */
    public function getExtension();
}
