<?php

namespace Oneup\UploaderBundle\Uploader\Response;

use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;

abstract class AbstractResponse implements \ArrayAccess, ResponseInterface
{
    protected $data;

    public function __construct()
    {
        $this->data = array();
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

    /**
     * The \ArrayAccess interface does not support multi-dimensional array syntax such as $array["foo"][] = bar
     * This function will take a path of arrays and add a new element to it, creating the path if needed.
     *
     * @param mixed $value
     * @param array $offsets
     *
     * @throws \InvalidArgumentException if the path contains non-array items.
     *
     */
    public function addToOffset($value, array $offsets)
    {
        $element =& $this->data;
        foreach ($offsets as $offset) {
            if (isset($element[$offset])) {
                if (is_array($element[$offset])) {
                    $element =& $element[$offset];
                } else {
                    throw new \InvalidArgumentException("The specified offset is set but is not an array at" . $offset);
                }
            } else {
                $element[$offset] = array();
                $element =& $element[$offset];
            }
        }
        $element[] = $value;
    }
}
