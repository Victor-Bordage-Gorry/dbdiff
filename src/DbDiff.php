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
            $dbC = new $conf['connector']($conf['host'], $conf['username'],$conf['password'],$conf['dbname']);
            $dbT = new $conf['translator']($dbC);
            $varname = 'db' . ($i +1);
            $$varname= $dbT;
        }
        if (isset($db1) && isset($db2)) {
            return new DbDiff($db1, $db2);
        } else {
            throw new \InvalidArgumentException('Error : not enough database\'s configuration');
        }
    }

    public function __construct(DbTranslator $db1, DbTranslator $db2) {
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
     * @param   string  $name
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
     * @param   string  $name
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
     * @param   array    $opts   associative array to manage the function returns
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

        foreach ($this->tables as $table_name) {
            // check tables
            if (!isset($this->schema1[$table_name])) {
                $this->updateComponentTable($this->db1->getDbName(), $table_name);
                continue;
            }

            if (!isset($this->schema2[$table_name])) {
                $this->updateComponentTable($this->db2->getDbName(), $table_name);
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
        foreach ($this->tables as $table_name) {
            if (!isset($this->schema1[$table_name]) || !isset($this->schema2[$table_name])) {
                continue;
            }

            $fields = array_merge($this->schema1[$table_name], $this->schema2[$table_name]);
            foreach ($fields as $field_name => $field) {
                if (!isset($this->schema1[$table_name][$field_name])) {
                    $this->updateComponentColumn($this->db1->getDbName(), $table_name, $field_name, true);
                    continue;
                }

                if (!isset($this->schema2[$table_name][$field_name])) {
                    $this->updateComponentColumn($this->db2->getDbName(), $table_name, $field_name, true);
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
        foreach ($this->tables as $table_name) {
            if (!isset($this->schema1[$table_name]) || !isset($this->schema2[$table_name])) {
                continue;
            }

            $fields = array_merge($this->schema1[$table_name], $this->schema2[$table_name]);
            foreach ($fields as $field_name => $field) {
                if (!isset($this->schema1[$table_name][$field_name]) && !isset($this->schema2[$table_name][$field_name])) {
                    continue;
                } elseif (!isset($this->schema1[$table_name][$field_name]) || isset($this->schema2[$table_name][$field_name])) {
                    $this->updateComponentColumn($this->db1->getDbName(), $table_name, $field_name, true, $this->schema2[$table_name][$field_name]['column']);
                    continue;
                } elseif (isset($this->schema1[$table_name][$field_name]) || !isset($this->schema2[$table_name][$field_name])) {
                    $this->updateComponentColumn($this->db2->getDbName(), $table_name, $field_name, true, $this->schema1[$table_name][$field_name]['column']);
                    continue;
                }

                $s1_params = $this->schema1[$table_name][$field_name]['column'];
                $s2_params = $this->schema2[$table_name][$field_name]['column'];

                foreach ($s1_params as $name => $details) {
                    if ($s1_params[$name] != $s2_params[$name]) {
                        $this->updateComponentColumn($this->db1->getDbName(), $table_name, $field_name, null, array($name => $s2_params[$name]));
                        $this->updateComponentColumn($this->db2->getDbName(), $table_name, $field_name, null, array($name => $s1_params[$name]));
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
        $return = array();
        foreach ($this->tables as $table_name) {
            if (!isset($this->schema1[$table_name]) || !isset($this->schema2[$table_name])) {
                continue;
            }

            $fields = array_merge($this->schema1[$table_name], $this->schema2[$table_name]);

            foreach ($fields as $field_name => $field) {
                if (!isset($this->schema1[$table_name][$field_name]['index']) && !isset($this->schema2[$table_name][$field_name]['index'])) {
                    continue;
                } elseif (!isset($this->schema1[$table_name][$field_name]['index']) || isset($this->schema2[$table_name][$field_name]['index'])) {
                    $this->updateComponentIndex($this->db1->getDbName(), $table_name, $field_name, true, $this->schema2[$table_name][$field_name]['index']);
                    continue;
                } elseif (isset($this->schema1[$table_name][$field_name]['index']) || !isset($this->schema2[$table_name][$field_name]['index'])) {
                    $this->updateComponentIndex($this->db2->getDbName(), $table_name, $field_name, true, $this->schema1[$table_name][$field_name]['index']);
                    continue;
                }

                $s1_params = $this->schema1[$table_name][$field_name]['index'];
                $s2_params = $this->schema2[$table_name][$field_name]['index'];

                $indexes = array_unique(array_merge(array_keys($s1_params), array_keys($s2_params)));
                foreach ($indexes as $index) {
                    if (empty($s1_params[$index])) {
                        $this->updateComponentIndex($this->db1->getDbName(), $table_name, $field_name, true, $this->schema2[$table_name][$field_name]['index'][$index]);
                        continue;
                    }
                    if (empty($s2_params[$index])) {
                        $this->updateComponentIndex($this->db2->getDbName(), $table_name, $field_name, true, $this->schema2[$table_name][$field_name]['index'][$index]);
                        continue;
                    }

                    foreach ($s1_params[$index] as $k => $v) {
                        if ($s1_params[$index][$k] !== $s2_params[$index][$k]) {
                            $this->updateComponentIndex($this->db1->getDbName(), $table_name, $field_name, null, array($k => $s2_params[$k]));
                            $this->updateComponentIndex($this->db2->getDbName(), $table_name, $field_name, null, array($k => $s1_params[$k]));
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
     * @param   string  $db_name
     * @param   string  $table_name
     */
    private function updateComponentTable($db_name, $table_name)
    {
        $db = $this->getDb($db_name);
        $table = new DbComponent\TableComponent($table_name);
        $table->missing(true);
        $db->setTable($table);
        $this->setDb($db);
    }

    /**
     * Update column data of a DbComponent object
     *
     * @param   string  $db_name
     * @param   string  $table_name
     * @param   string  $field_name
     * @param   boolean $missing
     * @param   array   $attributes
     */
    private function updateComponentColumn($db_name, $table_name, $field_name, $ismissing = null, $attributes = array())
    {
        $db = $this->getDb($db_name);
        if (!$table = $db->getTable($table_name)) {
            $table = new DbComponent\TableComponent($table_name);
        }
        if (!$column = $table->getColumn($field_name)) {
            $column = new DbComponent\ColumnComponent($field_name);
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
     * @param   string  $db_name
     * @param   string  $table_name
     * @param   string  $index_name
     * @param   boolean $missing
     * @param   array   $attributes
     */
    private function updateComponentindex($db_name, $table_name, $index_name, $ismissing = null, $attributes = array())
    {
        $db = $this->getDb($db_name);
        if (!$table = $db->getTable($table_name)) {
            $table = new DbComponent\TableComponent($table_name);
        }
        if (!$index = $table->getIndex($index_name)) {
            $index = new DbComponent\IndexComponent($index_name);
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
