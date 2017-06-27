<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Min
 * @package Cable\Validation\Rules
 */
class Max extends Rule
{

    /**
     * @var string
     */
    protected $name = 'max';

    /**
     * @var string
     */
    protected $errorMessage = '$0 field must have smaller value than $1';
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        $max = $this->getParameters()[0];

        return ($data < $max);
    }
}
