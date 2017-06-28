<?php

namespace Cable\Validation;


abstract class Filter
{

    /**
     * @var string
     */
    protected $name;



    /**
     * Filter constructor.
     * @param string $name
     */
    public function __construct($name = '')
    {
        $this->setName($name);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Filter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }



    /**
     * @param mixed $data
     * @return mixed
     */
    abstract public function execute($data);
}

