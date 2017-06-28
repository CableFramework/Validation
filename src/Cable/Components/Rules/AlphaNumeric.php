<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class AlphaNumeric
 * @package Cable\Validation\Rules
 */
class AlphaNumeric extends Rule
{

    /**
     * @var string
     */
    protected $name = 'alpha_numeric';

    /**
     * @var string
     */
    protected $errorMessage = '$0 fields value must be a valid alpha numeric value';

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        return (preg_match("#^[a-zA-ZÀ-ÿ0-9]+$#", $data) === 1);
    }
}