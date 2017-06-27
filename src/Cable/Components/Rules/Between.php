<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Min
 * @package Cable\Validation\Rules
 */
class Between extends Rule
{

    /**
     * @var string
     */
    protected $name = 'between';

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


        return ($data >= $min && $data < $max);
    }
}