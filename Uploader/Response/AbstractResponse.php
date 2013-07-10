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

    /**
     * The \ArrayAccess interface does not support multi-dimensional array syntax such as $array["foo"][] = bar
     * This function will take a path of arrays and add a new element to it.
     *
     * @param mixed $value
     * @param string $offset,...
     *
     * @throws \InvalidArgumentException if the path contains non-array or unset items.
     *
     */
    public function addToOffset()
    {
        $args = func_get_args();
        $value = $args[0];
        array_shift($args);
        $element =& $this->data;
        foreach ($args as $offset) {
            if (isset($element[$offset]) && is_array($element[$offset])) {
                $element =& $element[$offset];
            } else {
                throw new \InvalidArgumentException("The specified path does not exsist or is not an array at " . $offset);
            }
        }
        $element = $value;
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
}
