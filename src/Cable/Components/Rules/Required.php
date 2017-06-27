<?php

namespace Cable\Validation\Rules;

use Cable\Validation\Rule;

/**
 * Class Required
 * @package Cable\Validation\Rules
 */
class Required extends Rule
{

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        return $data !== null || $data !== '';
    }
}