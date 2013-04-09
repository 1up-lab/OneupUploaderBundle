<?php

namespace Oneup\UploaderBundle\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;

class FineUploaderResponse implements ResponseInterface
{
    protected $success;
    protected $error;
    protected $data;
    
    public function __construct()
    {
        $this->success = true;
        $this->error = null;
        $this->data = array();
    }
    
    public function assemble()
    {
        // explicitly overwrite success and error key
        // as these keys are used internaly by the
        // frontend uploader
        $data = $this->data;
        $data['success'] = $this->success;
        
        if($this->success)
            unset($data['error']);
        
        if(!$this->success)
            $data['error'] = $this->error;
        
        return $data;
    }

    public function offsetSet($offset, $value)
    {
        is_null($offset) ? $this->data[] = $value : $this->data[$offset] = $value;
    }
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
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
    
    public function setError($msg)
    {
        $this->error = $msg;
        
        return $this;
    }
    
    public function getError()
    {
        return $this->error;
    }
}