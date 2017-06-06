<?php

namespace Cable\Validation;


class Rulebag
{
    /**
     * @var array
     */
    private $rules;

    /**
     * Rulebag constructor.
     * @param array $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * @param Rule $rule
     * @return $this
     */
    public function add(Rule $rule){
        $this->rules[] = $rule;

        return $this;
    }
    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     * @return Rulebag
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }


}
