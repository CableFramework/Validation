<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

class Email extends Rule
{

    /**
     * @var string
     */
    protected $errorMessage = '$0 field must be valid email.';

    /**
     * @var string
     */
    protected $name = 'email';

    /**
     *
     * @return mixed
     */
    public function handle($data)
    {
        return filter_var($data, FILTER_VALIDATE_EMAIL);
    }
}