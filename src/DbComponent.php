<?php
namespace DbDiff;

class DbComponent {

    use DbComponent\TraitComponent;

    protected $tables = array();

    public function __construct(string $name) {
        $this->setName($name);
    }

    /**
     * ### SETTERS ###
     **/

    /**
     * Add multiple tables to the DbComponent object
     *
     * @param   array   $tables     array of TableComponent object
     */
    public function setTables(array $tables) {
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
     * @param   TableComponent  $table
     */
    public function setTable(DbComponent\TableComponent $table) {
        $this->tables[$table->getName()] = $table;
    }

    /**
     * ### GETTERS ###
     **/

    /**
     * Return all tables setted
     *
     * @return  array
     */
    public function getTables() {
        return $this->tables;
    }

    /**
     * Return all tables' name
     *
     * @return  array
     */
    public function getTablesName() {
        return array_keys($this->tables);
    }

    /**
     * Return TableComponent object added
     *
     * @param   string  $name   name of the table
     * @return  TableComponent
     * @throws  BadMethodCallException
     */
    public function getTable(string $name) {
        if (isset($this->tables[$name])) {
            return $this->tables[$name];
        } else {
            //throw new \BadMethodCallException('Error : table ' . $name . ' not found');
            return false;
        }
    }
}
