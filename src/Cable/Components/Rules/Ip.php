<?php
namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class Ip
 * @package Cable\Validation\Rules
 */
class Ip extends Rule
{

    /**
     * @var string
     */
    protected $name = 'ip';

    /**
     * @var string
     */
    protected $errorMessage = '$0 fields value must be a valid ip';

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
       return filter_var($data, FILTER_VALIDATE_IP);
    }
}