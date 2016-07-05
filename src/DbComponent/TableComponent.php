<?php

namespace DbDiff\DbComponent;

class TableComponent extends \DbDiff\DbComponent
{

    protected $columns = array();
    protected $indexes = array();
    protected $missing = false;

    /**
     * ### SETTERS ###
     **/

    /**
     * Add multiple column to the TableComponent object
     *
     * @param   array   $columns     array of ColumnComponent object
     */
    public function setColumns(array $columns)
    {
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
     * @param   ColumnComponent $column
     */
    public function setColumn(ColumnComponent $column)
    {
        $this->columns[$column->getName()] = $column;
    }

    /**
     * Add multiple index to the TableComponent object
     *
     * @param   array   $indexes     array of IndexComponent object
     */
    public function setIndexes(array $indexes)
    {
        if (empty($indexes) || !is_array($indexes)) {
            return false;
        }
        foreach ($indexes as $index) {
            $this->setIndex($index);
        }
    }

    /**
     * Add a Index to the TableComponent object
     *
     * @param   IndexComponent $column
     */
    public function setIndex(IndexComponent $index)
    {
        $this->indexes[$index->getName()] = $index;
    }


    /**
     * ### GETTERS ###
     **/

    /**
     * Return all columns setted
     *
     * @return  array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Return all columns' name
     *
     * @return  array
     */
    public function getColumnsName()
    {
        return array_keys($this->columns);
    }

    /**
     * Return ColumnComponent object added
     *
     * @param   string  $name   name of the column
     * @return  ColumnComponent
     * @throws  BadMethodCallException
     */
    public function getColumn(string $name)
    {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        } else {
            //throw new BadMethodCallException('Error : column ' . $name . ' not found');
            return false;
        }
    }

    /**
     * Return all indexes setted
     *
     * @return  array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Return all indexes' name
     *
     * @return  array
     */
    public function getIndexesName()
    {
        return array_keys($this->indexes);
    }

    /**
     * Return IndexComponent object added
     *
     * @param   string  $name   name of the index
     * @return  IndexComponent
     * @throws  BadMethodCallException
     */
    public function getIndex(string $name)
    {
        if (isset($this->indexes[$name])) {
            return $this->indexes[$name];
        } else {
            //throw new BadMethodCallException('Error : column ' . $name . ' not found');
            return false;
        }
    }
}
