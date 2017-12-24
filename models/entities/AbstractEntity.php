<?php
namespace entities;

abstract class AbstractEntity
{
    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}