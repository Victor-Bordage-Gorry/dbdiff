<?php
namespace DbDiff;

abstract class DbComponent
{

    protected $name;
    protected $missing;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * ### SETTERS ###
     **/

    /**
     * Set database's name
     *
     * @param   string  $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * ### GETTERS ###
     **/

    /**
     * Get database's name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * ### SPECIFICS FUNCTIONS ###
     **/

    /**
     * Set missing attribute and return the value. If $missing is null, the function return the value of the attribute
     *
     * @param   boolean $missing
     * @return  boolean
     */
    public function missing($missing = null)
    {
        if ($missing !== null) {
            if ($missing === false) {
                $this->missing = false;
            } else {
                $this->missing = true;
            }
        }
        return $this->missing;
    }
}
