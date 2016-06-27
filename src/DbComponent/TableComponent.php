<?php

namespace DbDiff\DbComponent;

class TableComponent {

    use TraitComponent;

    protected $columns;
    protected $indexes;
    protected $missing = false;

    public function __construct(string $name) {
        $this->setName($name);
    }

    /**
     * ### SETTERS ###
     **/

    /**
     * Add multiple column to the TableComponent object
     *
     * @param   array   $column     array of ColumnComponent object
     */
    public function setColumns(array $columns) {
        if (empty($columns) || !is_array($columns)) {
            return false;
        }
        foreach ($columns as $column) {
            $this->setColumn($column);
        }
    }

    /**
     * Add a Column to the TableComponent object
     *
     * @param   ColumnComponent $column [description]
     */
    public function setColumn(ColumnComponent $column) {
        $this->columns[$column->getName()] = $column;
    }


    /**
     * ### GETTERS ###
     **/

    /**
     * Return all columns setted
     *
     * @return  array
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Return all columns' name
     *
     * @return  array
     */
    public function getColumnsName() {
        return array_keys($this->columns);
    }

    /**
     * Return ColumnComponent object added
     *
     * @param   string  $name   name of the table
     * @return  ColumnComponent
     * @throws  BadMethodCallException
     */
    public function getColumn(string $name) {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        } else {
            //throw new BadMethodCallException('Error : column ' . $name . ' not found');
            return false;
        }
    }

}
