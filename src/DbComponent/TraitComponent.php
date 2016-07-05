<?php
namespace DbDiff\DbComponent;

trait TraitComponent
{

    protected $name;
    protected $missing;


    /**
     * Get component's name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set component's name
     *
     * @param   string  $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

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
