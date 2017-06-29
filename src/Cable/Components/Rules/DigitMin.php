<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Min
 * @package Cable\Validation\Rules
 */
class DigitMin extends Rule
{

    /**
     * @var string
     */
    protected $name = 'digit_min';

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

        $min = isset($params[0]) ? $params[0] : false;
        if (false === $min) {
            return false;
        }

        return (strlen((string) $data) >= $min);
    }
}