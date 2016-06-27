<?php

namespace DbDiff;

abstract class Connexion {

    protected $bdd;
    protected $dbName;

    public function __construct($host, $login, $password, $dbname) {
        $this->dbName = $dbname;
        $this->connectDb($host, $login, $password, $dbname);
    }

    /**
     *  Return the database's talbe schema
     *
     * @return array
     */
    public function getDbSchema() {
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
    public function getDbName() {
        return $this->dbName;
    }

    protected abstract function connectDb($host, $login, $password, $dbname);
    protected abstract function getDbTables();
    protected abstract function getTableSchema($tablename);
    protected abstract function query($query, $opts);
}
