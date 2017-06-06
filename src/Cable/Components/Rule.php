<?php

namespace Cable\Validation;


class Rule
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $error;

    /**
     * @var array
     */
    private $parameters;


    /**
     * @var mixed
     */
    private $callback;

    /**
     * Filter constructor.
     * @param string $name
     * @param array $parameters
     * @param string $error
     */
    public function __construct($name = '',array $parameters = [], $error = '', $callback = '')
    {
        $this->setName($name)
            ->setParameters($parameters)
            ->setError($error)
            ->setCallback($callback);
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
     * @return Rule
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
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
     * @return Rule
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     * @return Rule
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     * @return Rule
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }


}