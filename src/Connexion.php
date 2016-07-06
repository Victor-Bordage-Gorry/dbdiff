<?php

namespace DbDiff;

abstract class Connexion
{

    protected $bdd;
    protected $dbName;

    public function __construct($host, $login, $password, $dbname)
    {
        if (!defined('static::DB_TYPE')) {
            throw new \InvalidArgumentException('Constant DB_TYPE is not defined on subclass ' . get_class($this));
        }
        $this->dbName = $dbname;
        $this->connectDb($host, $login, $password, $dbname);
    }

    /**
     *  Return the database's talbe schema
     *
     * @return arrayn
     */
    public function getDbSchema()
    {
        $schema = [];
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

    abstract protected function connectDb($host, $login, $password, $dbname);
    abstract protected function getDbTables();
    abstract protected function getTableSchema($tablename);
    abstract protected function query($query, $opts);
}
