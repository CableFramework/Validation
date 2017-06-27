<?php

namespace Cable\Validation\Rules;


use Cable\Validation\Rule;

/**
 * Class MatchWith
 * @package Cable\Validation\Rules
 */
class MatchWith extends Rule
{
    /**
     * @var string
     */
    protected $errorMessage = '$0 field must match with $1 field.';

    /**
     * @var string
     */
    protected $name = 'match_with';

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        $target = $this->getParameters()[0];

        return  $data === $this->getDatas()[$target];
    }
}