<?php
namespace Cable\Validation;


class RuleResolver extends Rule
{

    /**
     * @var Rulebag
     */
    private $rules;

    /**
     * @var bool
     */
    private $strict;

    /**
     * RuleResolver constructor.
     * @param Rulebag $rules
     * @param bool $strict
     */
    public function __construct(Rulebag $rules, $strict = true)
    {
        $this->rules = $rules;
        $this->strict = $strict;

        /**
         * @var Rule $rule
         */



    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        $rules = $this->rules->getRules();

        // handle rules
        foreach ($rules as $rule){
            $result = $rule->handle($data);


            if ( !$result) {
                $this->setErrorMessage($rule->getErrorMessage())
                    ->setParameters($rule->getParameters())
                    ->setName($rule->getName());

                if ($this->strict === true) {
                    return;
                }
            }
        }
    }
}