<?php

namespace Cable\Validation\Filters;


use Cable\Validation\Filter;

/**
 * Class Trim
 * @package Cable\Validation\Filters
 */
class Trim extends Filter
{

    /**
     * @param mixed $data
     * @return mixed
     */
    public function execute($data)
    {
        return trim($data);
    }
}