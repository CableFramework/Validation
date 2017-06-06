<?php

namespace Cable\Validation;


class Filter
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $callback;

    /**
     * Filter constructor.
     * @param string $name
     * @param null $callback
     */
    public function __construct($name = '', $callback = null)
    {
        $this->setName($name)
            ->setCallback($callback);
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
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param mixed $callback
     * @return Filter
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

}

