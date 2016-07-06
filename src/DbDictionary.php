<?php

namespace DbDiff;

abstract class DbDictionary
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

    protected $dictionary = [];
    protected $db;


    public function __construct(\DbDiff\Connexion\Mysql $db)
    {
        if (!defined('static::DB_TYPE')) {
            throw new \InvalidArgumentException('Constant DB_TYPE is not defined on subclass ' . get_class($this));
        }
        $this->db = $db;
    }

    /**
     * Convert data's key
     *
     * @param  string   $data   data to translate
     * @return string           data translated
     */
    public function convertKeys($key)
    {
       if (isset($this->dictionary[$key])) {
            return $this->dictionary[$key];
        } else {
            throw new \InvalidArgumentException('Error in database\'s dictionary : ' . $key . ' not translated (' . $this->db::DB_TYPE . ' database).');
        }
    }

    /**
     * Hydrate the attribute dictonary
     *
     * @param   array   $data
     * @throws  InvalidArgumentException
     */
    public function translate($data)
    {
        $return = [];
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $return[$key] = $this->translate($val);
            } else {
                $return[$this->convertKeys($key)] = $val;
            }
        }
        return $return;
    }

    public function getTranslatedSchema() {
        return $this->translate($this->db->getDbSchema());
    }

    public function getDbName() {
        return $this->db->getDbName();
    }
}
