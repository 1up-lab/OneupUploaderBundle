<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Response;

abstract class AbstractResponse implements \ArrayAccess, ResponseInterface
{
    public function __construct(protected array $data = [])
    {
    }

    public function offsetSet($offset, $value): void
    {
        null === $offset ? $this->data[] = $value : $this->data[$offset] = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * The \ArrayAccess interface does not support multidimensional array syntax such as $array["foo"][] = bar
     * This function will take a path of arrays and add a new element to it, creating the path if needed.
     *
     * @throws \InvalidArgumentException if the path contains non-array items
     */
    public function addToOffset(array $value, array $offsets): void
    {
        $element = &$this->data;
        foreach ($offsets as $offset) {
            if (isset($element[$offset])) {
                if (\is_array($element[$offset])) {
                    $element = &$element[$offset];
                } else {
                    throw new \InvalidArgumentException('The specified offset is set but is not an array at' . $offset);
                }
            } else {
                $element[$offset] = [];
                $element = &$element[$offset];
            }
        }
        $element[] = $value;
    }
}
