<?php

namespace Cable\Validation;


abstract class Filter
{

    /**
     * @var string
     */
    protected $name;



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

