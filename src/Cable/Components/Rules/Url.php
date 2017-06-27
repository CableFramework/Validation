<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

class Url extends Rule
{


    /**
     * @var string
     */
    protected $errorMessage = '$0 field must be valid url.';

    /**
     * @var string
     */
    protected $name = 'url';

    /**
     * @param string $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        return filter_var($data, FILTER_VALIDATE_URL);

    }
}