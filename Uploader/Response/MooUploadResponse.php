<?php

namespace Oneup\UploaderBundle\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class MooUploadResponse extends AbstractResponse
{
    protected $id;
    protected $name;
    protected $size;
    protected $error;
    protected $finish;
    protected $uploadedName;

    public function __construct()
    {
        $this->finish = true;
        $this->error = 0;

        parent::__construct();
    }

    public function assemble()
    {
        $data = $this->data;

        $data['id'] = $this->id;
        $data['name'] = $this->name;
        $data['size'] = $this->size;
        $data['error'] = $this->error;
        $data['finish'] = $this->finish;
        $data['upload_name'] = $this->uploadedName;

        return $data;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setFinish($finish)
    {
        $this->finish = $finish;

        return $this;
    }

    public function getFinish()
    {
        return $this->finish;
    }

    public function setUploadedName($name)
    {
        $this->uploadedName = $name;

        return $this;
    }

    public function getUploadedName()
    {
        return $this->uploadedName;
    }
}
