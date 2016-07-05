<?php

namespace DbDiff\DbComponent;

class ColumnComponent extends \DbDiff\DbComponent
{

    protected $attributes = array();

    /**
     * ### SETTERS ###
     **/

    /**
     * Add multiple attributes to the ColumnComponent object
     *
     * @param   array   $attributes     array of attributes ('name' => 'value')
     */
    public function setAttributes(array $attributes)
    {
        if (empty($attributes) || !is_array($attributes)) {
            return false;
        }
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }

    /**
     * Add a attribute to the ColumnComponent object
     *
     * @param string    $name
     * @param string    $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * ### GETTERS ###
     **/

    /**
     * Return all attributes setted
     *
     * @return  array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Return all attributes' name
     *
     * @return  array
     */
    public function getAttributesName()
    {
        return array_keys($this->attributes);
    }

    /**
     * Return attribute
     *
     * @param   string   $name   name of the attribute
     * @return  string
     * @throws  BadMethodCallException
     */
    public function getAttribute(string $name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        } else {
            //throw new BadMethodCallException('Error : column ' . $name . ' not found');
            return false;
        }
    }
}
