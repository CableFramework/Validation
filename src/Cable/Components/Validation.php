<?php

namespace Cable\Validation;

/**
 * Class Validation
 * @package Cable\Validation
 */
class Validation
{
    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    /**
     * @var FilterRepository
     */
    private $filterRepository;


    /**
     * @var array
     */
    protected $errors = [];


    /**
     * @var array
     */
    protected static $messages = [
        'default' => '%s is not valid',
    ];



    /**
     * Validation constructor.
     * @param RuleRepository $ruleRepository
     * @param FilterRepository $filterRepository
     */
    public function __construct(RuleRepository $ruleRepository, FilterRepository $filterRepository)
    {
        $this->ruleRepository = $ruleRepository;
        $this->filterRepository = $filterRepository;
    }

    /**
     * @param array $datas
     * @param array $rules
     * @param array $filters
     * @param bool $strict
     * @return static
     */
    public function make(array $datas = [], array $rules = [], array $filters = [], $strict = true)
    {
        return $this->run($datas, $rules, $filters, $strict);

    }


    /**
     * @param array $datas
     * @param array $rules
     * @param array $filters
     * @param bool $strict
     * @throws ValidationException
     * @return $this
     */
    public function run(array $datas, array $rules,array  $filters, $strict = true)
    {
        foreach ($rules as $key => $rule){
            $data = isset($datas[$key]) ? $datas[$key] : nulll;

            if (isset($filters[$key])) {
                $data = $this->resolveFilterbag($this->prepareFilter($filters[$key]));
            }

            $this->resolveRulebag(array($key, $data), $this->prepareRules($rule));
        }


        return $this;
    }

    /**
     * @param $data
     * @param $rules
     * @param bool $strict
     */
    private function resolveRulebag(array $variables, Rulebag $rules, $strict = false)
    {
        list($key, $data) = $variables;

        $resolver = new RuleResolver($rules, $strict);

        $resolved = $resolver->handle($data);


        if (!$resolved) {
            $this->errors = $this->prepareErrorMessage(
                $key,
                $resolver->getErrorMessage(),
                $resolver->getParameters()
            );
        }
    }



    /**
     * @param mixed $data
     * @param Filterbag $filters
     * @return mixed
     */
    private function resolveFilterbag($data,Filterbag $filters)
    {
        return (new FilterResolver($filters))->execute($data);
    }

    /**
     * @param string $filter
     * @return Filterbag
     * @throws ValidationException
     */
    private function prepareFilter($filter)
    {
        if ($filter instanceof Filterbag) {
            return $filter;
        }

        $filters = explode('|', $filter);

        $filterBag = new Filterbag();
        foreach ($filters as $item) {

            if ( ! $this->filterRepository->has($item)) {
                throw new ValidationException(
                    sprintf(
                        '%s filter does not exists',
                        $item
                    )
                );
            }

            $filterBag->add($this->filterRepository->get($item));
        }

        return $filterBag;
    }

    /**
     * @param string $rule
     * @param array $datas
     * @throws ValidationException
     * @return Rulebag
     */
    private function prepareRules($rule, array $datas = [])
    {
        if ($rule instanceof Rulebag) {
            return $rule;
        }

        $exploded = explode('|', $rule);

        $ruleBag = new Rulebag();
        foreach ($exploded as $item) {

            list($name, $parameters) = $this->prepareRuleParameters($item);

            if ( !$this->ruleRepository->has($name)) {
                throw new ValidationException(
                    sprintf('%s rule not found', $name)
                );
            }

            $rule = $this->ruleRepository->get($name);


            $rule->setParameters(!empty($parameters) ? $parameters : [])
                ->setDatas($datas);

            $ruleBag->add($rule);
        }

        return $ruleBag;
    }

    /**
     * @param string $item
     * @return array
     */
    private function prepareRuleParameters($item)
    {
        if (false !== strpos($item, ':')) {
            return explode(':', $item);
        }

        return array($item, array());
    }

    /**
     * @param $field
     * @param $message
     * @param $args
     * @return mixed
     */
    private function prepareErrorMessage($field, $message, $args)
    {
        $args = array_merge([$field], $args);
        foreach ($args as $index => $arg) {
            $message = str_replace('$'.$index, $arg, $message);
        }

        return $message;
    }


    /**
     * @return bool
     */
    public function failed()
    {
        return ! empty($this->errors);
    }

    /**
     * @return array
     */
    public function failings()
    {
        return $this->errors;
    }
}