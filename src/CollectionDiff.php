<?php

namespace DbDiff;

abstract class CollectionDiff {

    protected $collection;

    public function __set($name, $data) {
        $this->collection[$name] = $data;
    }

    public function __get($collection) {
        if (isset($this->collection[$collection])) {
            return $this->collection[$collection];
        }
        return false;
    }

    public function getCollection() {
        return $this->collection;
    }

    public function getCollectionKeys() {
        return array_keys($this->collection);
    }

}
