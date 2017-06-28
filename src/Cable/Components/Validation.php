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
        }

        foreach ($datas as $key => $data){
            if (isset($filters[$key])) {
                $data = $this->resolveFilterbag($this->prepareFilter($data));
            }

            if (isset($rules[$key])) {
                $this->resolveRulebag($data, $this->prepareRules($rules[$key], $datas), $strict);
            }

        }

        #$this->handleRules($datas, $this->prepareRules($rules));


        return $this;
    }

    /**
     * @param $data
     * @return mixed|string
     */
    protected function handleFilterXss($data)
    {
        return $this->cleanInput($data, 0);
    }



    /**
     * @param $data
     * @param $rules
     * @param bool $strict
     */
    private function resolveRulebag($data, Rulebag $rules, $strict = false)
    {
        $rules = $rules->getRules();

        foreach ($rules as $rule) {

            /**
             * @var Rule $rule
             */

            $result = $rule->handle($data);

            if (!$result) {
                $this->errors = $this->prepareErrorMessage(
                    $data,
                    $rule->getErrorMessage(),
                    $rule->getParameters()
                );


                // if we are running in strict mode, then first failiure will stop the process
                if ($strict === true) {
                    return;
                }
            }

        }
    }



    /**
     * @param mixed $data
     * @param Filterbag $filters
     * @return mixed
     */
    private function resolveFilterbag($data, $filters)
    {
        $filters = $filters->getFilters();

        foreach ($filters as $filter) {
            /**
             * @var Filter $filter
             */

            $data = $filter->execute($data);
        }

        return $data;
    }



    /**
     * @param $data
     * @return string
     */
    protected function handleFilterStripTags($data)
    {
        return strip_tags(htmlentities(htmlspecialchars($data)));
    }


    /**
     * @param string $filter
     * @return Filterbag
     * @throws ValidationException
     */
    private function prepareFilter($filter)
    {
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

            $this->filterRepository->get($item);

            $filterBag->add($filter);
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