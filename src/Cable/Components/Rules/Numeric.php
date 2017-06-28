<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Numeric
 * @package Cable\Validation\Rules
 */
class Numeric extends Rule
{

    /**
     * @var string
     */
    protected $name = 'numeric';

    /**
     * @var string
     */
    protected $errorMessage = '$s fields must be numeric';

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
         return is_numeric($data);
    }
}