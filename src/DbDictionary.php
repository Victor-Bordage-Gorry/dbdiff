<?php

namespace DbDiff;

class DbDictionary
{

    const COLUMN_FIELD = 'field';
    const COLUMN_TYPE = 'type';
    const COLUMN_COLLATION = 'collation';
    const COLUMN_NULL = 'null';
    const COLUMN_KEY = 'key';
    const COLUMN_DEFAULT = 'default';
    const COLUMN_EXTRA = 'extra';
    const COLUMN_PRIVILEGES = 'privileges';
    const COLUMN_COMMENT = 'comment';
    const INDEX_TABLE = 'table';
    const INDEX_NON_UNIQUE = 'non_unique';
    const INDEX_KEY_NAME = 'key_name';
    const INDEX_SEQ_IN_INDEX = 'seq_in_index';
    const INDEX_COLUMN_NAME = 'column_name';
    const INDEX_COLLATION = 'collation';
    const INDEX_CARDINALITY = 'cardinality';
    const INDEX_SUB_PART = 'sub_part';
    const INDEX_PACKED = 'packed';
    const INDEX_NULL = 'null';
    const INDEX_INDEX_TYPE = 'index_type';
    const INDEX_COMMENT = 'comment';
    const INDEX_INDEX_COMMENT = 'index_comment';

    private $dictionary = array();

    public function convertKeys($data)
    {

        if (empty($data)) {
            return $data;
        }
        if (!is_array($data)) {
            return $this->dictionary[$data];
        }

        $return = array();
        foreach ($data as $key => $val) {
            if (isset($this->dictionary[$key])) {
                $return[$this->dictionary[$key]] = $val;
            }
        }
        return $return;
    }

    public function hydrateDictionary($data)
    {
        $this->checkDictionaryValidity($data);
        foreach ($data as $const => $key) {
            if ($this->getConstant($const)) {
                $this->dictionary[$key] = $const;
            } else {
                throw new \InvalidArgumentException('Error in database\'s dictionary : ' . $const . ' for ' . $key . ' isn\'t valid value.');
            }
        }
    }

    private function checkDictionaryValidity($data)
    {
        $consts = $this->getConstants();
        foreach ($consts as $key => $val) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException('Missing information in database\'s dictionary : ' . $key . ' not translated.');
            }
        }
    }

    private static function getConstants()
    {
        $reflect = new \ReflectionClass(__CLASS__);
        return $reflect->getConstants();
    }

    private static function getConstant($const)
    {
        $reflect = new \ReflectionClass(__CLASS__);
        return $reflect->getConstant($const);
    }
}
