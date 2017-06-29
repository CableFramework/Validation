<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Alpha
 * @package Cable\Validation\Rules
 */
class Alpha extends Rule
{

    /**
     * @var string
     */
    protected $name = 'alpha';

    /**
     * @var string
     */
    protected $errorMessage = '$0 fields value must be a valid alpha value';

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        return (preg_match('#^[a-zA-ZÀ-ÿ]+$#', $data) === 1);
    }
}
