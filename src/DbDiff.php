<?php

namespace DbDiff;

class DbDiff
{

    protected $db1;
    protected $db2;
    protected $schema1;
    protected $schema2;
    protected $tables1;
    protected $tables2;
    protected $tables;

    protected $dbs;

    /**
     * Build DbDiff object from config array
     *
     * @param  array  $config  databases configurations
     * @return DbDiff          new Dbdiff
     */
    public static function buildFromConfig(array $config)
    {
        for ($i = 0; $i < 2; $i++) {
            $conf = $config[$i];
            if (empty($conf['host']) || empty($conf['username']) || empty($conf['password']) || empty($conf['dbname'])) {
                throw new \InvalidArgumentException('Error in database\'s configuration : argument(s) missing');
            }
            if (!class_exists($conf['connector'])) {
                throw new \InvalidArgumentException('Error : unreconized connector');
            }
            if (!class_exists($conf['translator'])) {
                throw new \InvalidArgumentException('Error : unreconized translator');
            }
            $dbC = new $conf['connector']($conf['host'], $conf['username'], $conf['password'], $conf['dbname']);
            $dbT = new $conf['translator']($dbC);
            $varname = 'db' . ($i + 1);
            $$varname = $dbT;
        }
        if (isset($db1) && isset($db2)) {
            return new DbDiff($db1, $db2);
        } else {
            throw new \InvalidArgumentException('Error : not enough database configuration');
        }
    }

    /**
     * Constructor : Need 2 DbTranslator
     *
     * @param DbTranslator $db1 Translator of the first database
     * @param DbTranslator $db2 Translator of the second database
     */
    public function __construct(DbTranslator $db1, DbTranslator $db2)
    {
        $this->db1 = $db1;
        $this->db2 = $db2;

        $this->schema1 = $db1->getTranslatedSchema();
        $this->schema2 = $db2->getTranslatedSchema();

        $this->tables1 = array_keys($this->schema1);
        $this->tables2 = array_keys($this->schema2);

        $this->tables = array_unique(array_merge($this->tables1, $this->tables2));

        $this->setDb(new \DbDiff\DbComponent\DbComponent($this->db1->getDbName(), $this->db1::DB_TYPE));
        $this->setDb(new \DbDiff\DbComponent\DbComponent($this->db2->getDbName(), $this->db2::DB_TYPE));
    }

    /**
     * ### SETTERS ###
     **/

    /**
     * Set a DbComponent to the dbs attribute
     *
     * @param   DbComponent  $db  database object
     */
    private function setDb(DbComponent $db)
    {
        $this->dbs[$db->getName()] = $db;
    }

    /**
     * ### GETTERS ###
     **/

    /**
     * Return the DbComponent of the selected database
     *
     * @param   string  $name database's name
     * @return  DbComponent
     * @throws  BadMethodCallException
     */
    public function getDb($name)
    {
        if (isset($this->dbs[$name])) {
            return $this->dbs[$name];
        } else {
            throw new \BadMethodCallException('Error : database ' . $name . ' not found');
            return false;
        }
    }

    /**
     * Get the attribute dbs
     *
     * @return  array
     */
    public function getDbs()
    {
        return $this->dbs;
    }

    /**
     * ### SPECIFICS FUNCTIONS ###
     **/

    /**
     * Compare 2 databases' schema
     *
     * @return  array
     */
    public function compare()
    {
        $this->compareTables();
        $this->compareColumns();
        $this->compareColumnsAttribute();
        $this->compareColumnsIndex();
    }

    /**
     * Check db's tables differences
     *
     * @return  array
     */
    public function compareTables()
    {
        foreach ($this->tables as $tableName) {
            // check tables
            if (!isset($this->schema1[$tableName])) {
                $this->updateComponentTable($this->db1->getDbName(), $tableName);
                continue;
            }

            if (!isset($this->schema2[$tableName])) {
                $this->updateComponentTable($this->db2->getDbName(), $tableName);
                continue;
            }
        }
    }

    /**
     * Check table's columns differences
     *
     * @return  array
     */
    public function compareColumns()
    {
        foreach ($this->tables as $tableName) {
            if (!isset($this->schema1[$tableName]) || !isset($this->schema2[$tableName])) {
                continue;
            }

            $fields = array_merge($this->schema1[$tableName], $this->schema2[$tableName]);
            foreach ($fields as $fieldName => $field) {
                if (!isset($this->schema1[$tableName][$fieldName])) {
                    $this->updateComponentColumn($this->db1->getDbName(), $tableName, $fieldName, true);
                    continue;
                }

                if (!isset($this->schema2[$tableName][$fieldName])) {
                    $this->updateComponentColumn($this->db2->getDbName(), $tableName, $fieldName, true);
                    continue;
                }
            }
        }
    }

    /**
     * Check column's attributes differences
     *
     * @return  array
     */
    public function compareColumnsAttribute()
    {
        foreach ($this->tables as $tableName) {
            if (!isset($this->schema1[$tableName]) || !isset($this->schema2[$tableName])) {
                continue;
            }

            $fields = array_merge($this->schema1[$tableName], $this->schema2[$tableName]);
            foreach ($fields as $fieldName => $field) {
                if (!isset($this->schema1[$tableName][$fieldName]) && !isset($this->schema2[$tableName][$fieldName])) {
                    continue;
                } elseif (!isset($this->schema1[$tableName][$fieldName]) || isset($this->schema2[$tableName][$fieldName])) {
                    $this->updateComponentColumn($this->db1->getDbName(), $tableName, $fieldName, true, $this->schema2[$tableName][$fieldName]['column']);
                    continue;
                } elseif (isset($this->schema1[$tableName][$fieldName]) || !isset($this->schema2[$tableName][$fieldName])) {
                    $this->updateComponentColumn($this->db2->getDbName(), $tableName, $fieldName, true, $this->schema1[$tableName][$fieldName]['column']);
                    continue;
                }

                $s1Params = $this->schema1[$tableName][$fieldName]['column'];
                $s2Params = $this->schema2[$tableName][$fieldName]['column'];

                foreach ($s1Params as $name => $details) {
                    if ($s1Params[$name] != $s2Params[$name]) {
                        $this->updateComponentColumn($this->db1->getDbName(), $tableName, $fieldName, null, [$name => $s2Params[$name]]);
                        $this->updateComponentColumn($this->db2->getDbName(), $tableName, $fieldName, null, [$name => $s1Params[$name]]);
                    }
                }
            }
        }
    }

    /**
     * Check column's indexes differences
     *
     * @return  array
     */
    public function compareColumnsIndex()
    {
        $return = [];
        foreach ($this->tables as $tableName) {
            if (!isset($this->schema1[$tableName]) || !isset($this->schema2[$tableName])) {
                continue;
            }

            $fields = array_merge($this->schema1[$tableName], $this->schema2[$tableName]);

            foreach ($fields as $fieldName => $field) {
                if (!isset($this->schema1[$tableName][$fieldName]['index']) && !isset($this->schema2[$tableName][$fieldName]['index'])) {
                    continue;
                } elseif (!isset($this->schema1[$tableName][$fieldName]['index']) || isset($this->schema2[$tableName][$fieldName]['index'])) {
                    $this->updateComponentIndex($this->db1->getDbName(), $tableName, $fieldName, true, $this->schema2[$tableName][$fieldName]['index']);
                    continue;
                } elseif (isset($this->schema1[$tableName][$fieldName]['index']) || !isset($this->schema2[$tableName][$fieldName]['index'])) {
                    $this->updateComponentIndex($this->db2->getDbName(), $tableName, $fieldName, true, $this->schema1[$tableName][$fieldName]['index']);
                    continue;
                }

                $s1Params = $this->schema1[$tableName][$fieldName]['index'];
                $s2Params = $this->schema2[$tableName][$fieldName]['index'];

                $indexes = array_unique(array_merge(array_keys($s1Params), array_keys($s2Params)));
                foreach ($indexes as $index) {
                    if (empty($s1Params[$index])) {
                        $this->updateComponentIndex($this->db1->getDbName(), $tableName, $fieldName, true, $this->schema2[$tableName][$fieldName]['index'][$index]);
                        continue;
                    }
                    if (empty($s2Params[$index])) {
                        $this->updateComponentIndex($this->db2->getDbName(), $tableName, $fieldName, true, $this->schema2[$tableName][$fieldName]['index'][$index]);
                        continue;
                    }

                    foreach ($s1Params[$index] as $k => $v) {
                        if ($s1Params[$index][$k] !== $s2Params[$index][$k]) {
                            $this->updateComponentIndex($this->db1->getDbName(), $tableName, $fieldName, null, [$k => $s2Params[$k]]);
                            $this->updateComponentIndex($this->db2->getDbName(), $tableName, $fieldName, null, [$k => $s1Params[$k]]);
                        }
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Update table data of a DbComponent object
     *
     * @param   string  $dbName    database's name to update
     * @param   string  $tableName  Table'name to set
     */
    private function updateComponentTable($dbName, $tableName)
    {
        $db = $this->getDb($dbName);
        $table = new DbComponent\TableComponent($tableName);
        $table->missing(true);
        $db->setTable($table);
        $this->setDb($db);
    }

    /**
     * Update column data of a DbComponent object
     *
     * @param   string  $dbName     database's name
     * @param   string  $tableName  table's name
     * @param   string  $fieldName  field's name to set
     * @param   bool    $missing    if boolean, set the attribute "missing"
     * @param   array   $attributes array of the attributes to set
     */
    private function updateComponentColumn($dbName, $tableName, $fieldName, $ismissing = null, $attributes = [])
    {
        $db = $this->getDb($dbName);
        if (!$table = $db->getTable($tableName)) {
            $table = new DbComponent\TableComponent($tableName);
        }
        if (!$column = $table->getColumn($fieldName)) {
            $column = new DbComponent\ColumnComponent($fieldName);
        }
        if (is_bool($ismissing)) {
            $column->missing($ismissing);
        }
        $column->setAttributes($attributes);

        $table->setColumn($column);
        $db->setTable($table);
        $this->setDb($db);
    }

    /**
     * Update index data of a DbComponent object
     *
     * @param   string  $dbName      database's name
     * @param   string  $tableName   table's name
     * @param   string  $indexName  index's name to set
     * @param   bool    $missing     if boolean, set the attribute "missing"
     * @param   array   $attributes  array of the attributes to set
     */
    private function updateComponentindex($dbName, $tableName, $indexName, $ismissing = null, $attributes = [])
    {
        $db = $this->getDb($dbName);
        if (!$table = $db->getTable($tableName)) {
            $table = new DbComponent\TableComponent($tableName);
        }
        if (!$index = $table->getIndex($indexName)) {
            $index = new DbComponent\IndexComponent($indexName);
        }
        if (is_bool($ismissing)) {
            $index->missing($ismissing);
        }
        if (!empty($attributes)) {
            foreach ($attributes as $name => $data) {
                $index->setAttributes($data);
            }
        }
        $table->setIndex($index);
        $db->setTable($table);
        $this->setDb($db);
    }
}
