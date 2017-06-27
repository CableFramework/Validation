<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Min
 * @package Cable\Validation\Rules
 */
class Min extends Rule
{

    /**
     * @var string
     */
    protected $name = 'min';

    /**
     * @var string
     */
    protected $errorMessage = '$0 field must have bigger value than $1';
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        $min = $this->getParameters()[0];

        return ($data >= $min);
    }
}