<?php

namespace Oneup\UploaderBundle\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class FineUploaderResponse extends AbstractResponse
{
    protected $success;
    protected $error;
    protected $preventRetry;

    public function __construct()
    {
        $this->success      = true;
        $this->error        = null;
        $this->preventRetry = false;

        parent::__construct();
    }

    public function assemble()
    {
        // explicitly overwrite success and error key
        // as these keys are used internally by the
        // frontend uploader
        $data = $this->data;
        $data['success'] = $this->success;

        if($this->success)
            unset($data['error']);

        if(!$this->success) {
            $data['error'] = $this->error;

            //setting this will disable the retry on the frontend
            if ($this->preventRetry) {
                $data['preventRetry'] = true;
            }
        }

        return $data;
    }

    public function setSuccess($success)
    {
        $this->success = (bool) $success;

        return $this;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function setError($msg, $preventRetry = false)
    {
        $this->error        = $msg;
        $this->preventRetry = !empty($preventRetry);

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }
}
