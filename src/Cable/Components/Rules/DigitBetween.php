<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Min
 * @package Cable\Validation\Rules
 */
class DigitBetween extends Rule
{

    /**
     * @var string
     */
    protected $name = 'digit_between';

    /**
     * @var string
     */
    protected $errorMessage = '$0 field\'s value must be between $1 and $2';

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        list($min, $max) = $this->getParameters();

        return (strlen($data) >= $min && strlen($data) < $max);
    }
}