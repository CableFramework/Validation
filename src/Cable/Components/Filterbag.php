<?php

namespace Cable\Validation;


class Filterbag
{
    /**
     * @var array
     */
    private $filters;

    /**
     * Rulebag constructor.
     * @param array $rules
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @param Rule $rule
     * @return $this
     */
    public function add(Filter $filter){
        $this->filters[] = $filter;

        return $this;
    }
    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $rules
     * @return Rulebag
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }


}
