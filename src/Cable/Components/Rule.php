<?php

namespace Cable\Validation;


abstract class Rule
{

    /**
     * @var string
     */
    protected $name;


    /**
     * @var array
     */
    private $datas;

    /**
     * @var array
     */
    protected $parameters;


    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    abstract public function handle($data);


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
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return Rule
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }


    /**
     * @return array
     */
    public function getDatas(){
        return $this->datas;
    }

    /**
     * @param array $datas
     * @return $this
     */
    public function setDatas(array  $datas){
        $this->datas = $datas;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     * @return Rule
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }
}