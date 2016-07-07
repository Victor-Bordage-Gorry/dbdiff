<?php

namespace DbDiff;

abstract class DbConnector
{

    protected $bdd;
    protected $dbName;

    /**
     * Constructor : set the dabase's connection
     *
     * @param string $host     database's host
     * @param string $login    database's login
     * @param string $password database's password
     * @param string $dbname   database's name
     */
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

    /**
     * Abstract : function to connect to the database
     *
     * @param string $host     database's host
     * @param string $login    database's login
     * @param string $password database's password
     * @param string $dbname   database's name
     * @return object          database object
     */
    abstract protected function connectDb($host, $login, $password, $dbname);

    /**
     * Return database's tables name
     *
     * @return array database's table name
     */
    abstract protected function getDbTables();

    /**
     * Return table schema
     *
     * @param  string $tablename table's name
     * @return array    table schema
     */
    abstract protected function getTableSchema($tablename);

    /**
     * Launch a query
     * @param  string $query query to launch
     * @param  array $opts   optionnal data
     * @return mixed
     */
    abstract protected function query($query, $opts);
}
