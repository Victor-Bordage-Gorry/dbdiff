<?php

namespace DbDiff\DbComponent;

use \DbDiff\DbComponent as Dbc;

class DbComponent extends Dbc
{

    protected $tables = [];
    protected $type;

    /**
     *  The constructor need the database's name and type
     * @param string $name database's name
     * @param string $type database's tye
     */
    public function __construct(string $name, string $type)
    {
        $this->setName($name);
        $this->setType($type);
    }

    /**
     * ### SETTERS ###
     **/

    /**
     * Add multiple tables to the DbComponent object
     *
     * @param   array   $tables     array of TableComponent object
     */
    public function setTables(array $tables)
    {
        if (empty($tables) || !is_array($tables)) {
            return false;
        }

        foreach ($tables as $table) {
            $this->setTable($table);
        }
    }

    /**
     * Add a table to the DbComponent object
     *
     * @param   TableComponent  $table Table to add
     */
    public function setTable(TableComponent $table)
    {
        $this->tables[$table->getName()] = $table;
    }

    /**
     * Set database's name
     *
     * @param   string  $name type's name
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * ### GETTERS ###
     **/

    /**
     * Return all tables setted
     *
     * @return  array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Return all tables' name
     *
     * @return  array
     */
    public function getTablesName()
    {
        return array_keys($this->tables);
    }

    /**
     * Return TableComponent object added
     *
     * @param   string  $name   name of the table
     * @return  TableComponent
     * @throws  BadMethodCallException
     */
    public function getTable(string $name)
    {
        if (isset($this->tables[$name])) {
            return $this->tables[$name];
        } else {
            //throw new \BadMethodCallException('Error : table ' . $name . ' not found');
            return false;
        }
    }

    /**
     * Get database's type
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }
}
