<?php

namespace DbDiff;

abstract class Connexion
{

    protected $bdd;
    protected $dbName;
    protected $dictionary = array();

    public function __construct($host, $login, $password, $dbname)
    {
        if (!defined('static::DB_TYPE')) {
            throw new \InvalidArgumentException('Constant DB_TYPE is not defined on subclass ' . get_class($this));
        }
        if (!is_array($this->dictionary) || empty($this->dictionary)) {
            throw new \InvalidArgumentException('Attribute dictionary is not correctly defined in ' . get_class($this));
        }
        $this->dbName = $dbname;
        $this->connectDb($host, $login, $password, $dbname);
    }

    /**
     *  Return the database's talbe schema
     *
     * @return array
     */
    public function getDbSchema()
    {
        $schema = array();
        $dbSchema = $this->getDbTables();
        if (!empty($dbSchema)) {
            foreach ($dbSchema as $table) {
                $tableSchema = $this->getTableSchema($table);
                $schema[$table] = $tableSchema;
            }
        }
        return $schema;
    }

    /**
     * Return the current database's name
     *
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Return database's dictionary
     *
     * @return array
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }

    abstract protected function connectDb($host, $login, $password, $dbname);
    abstract protected function getDbTables();
    abstract protected function getTableSchema($tablename);
    abstract protected function query($query, $opts);
}
