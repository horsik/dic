<?php

namespace Kampaw\Dic;

class ObjectStorage
{
    /**
     * @var array $objects
     */
    protected $objects = array();

    /**
     * @param object $object
     */
    public function attach($object)
    {
        $hash = spl_object_hash($object);

        $this->objects[$hash] = $object;
    }

    /**
     * @param object $object
     */
    public function detach($object)
    {
        $hash = spl_object_hash($object);

        unset($this->objects[$hash]);
    }

    /**
     * @return object
     */
    public function end()
    {
        end($this->objects);

        return current($this->objects);
    }

    /**
     * @param object $object
     * @return bool
     */
    public function contains($object)
    {
        $hash = spl_object_hash($object);

        return isset($this->objects[$hash]);
    }

    /**
     * @param object $object
     * @param int|null $length
     * @return array
     */
    public function slice($object, $length = null)
    {
        $hash = spl_object_hash($object);
        $keys = array_keys($this->objects);

        $offset = array_search($hash, $keys);

        return array_slice($this->objects, $offset, $length);
    }
}