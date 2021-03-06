<?php

namespace Cable\Validation;


class FilterRepository
{

    /**
     * @var Filter[]
     */
    private $filters;

    /**
     * FilterRepository constructor.
     * @param Filter[] $filters
     */
    public function __construct(array $filters)
    {
        if ( !empty($filters)) {
            foreach ($filters as $filter){
                $this->addFilter($filter);
            }
        }

    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name){
        return isset($this->filters[$name]);
    }

    /**
     * @param string $name
     * @return Filter
     */
    public function get($name){
        return $this->filters[$name];
    }
    /**
     * @param Filter $filter
     * @return $this
     */
    public function addFilter(Filter $filter){
        $this->filters[$filter->getName()] = $filter;

        return $this;
    }

    /**
     * @return Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param Filter[] $filters
     * @return FilterRepository
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }
}