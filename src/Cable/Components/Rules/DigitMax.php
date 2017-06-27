<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Min
 * @package Cable\Validation\Rules
 */
class DigitMax extends Rule
{

    /**
     * @var string
     */
    protected $name = 'digit_max';

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
        $params = $this->getParameters();

        $max = isset($params[0]) ? $params[0] : false;
        if (false === $max) {
            return false;
        }


        return (strlen($data) < $max);
    }
}