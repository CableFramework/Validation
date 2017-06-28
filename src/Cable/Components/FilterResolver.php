<?php
namespace Cable\Validation;


class FilterResolver extends Filter
{

    /**
     * @var Filterbag
     */
    private $filters;

    /**
     * FilterGroup constructor.
     * @param Filterbag $filterbag
     */
    public function __construct(Filterbag $filterbag)
    {
        $this->filters = $filterbag;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function execute($data)
    {
        $filters = $this->filters->getFilters();
        foreach ($filters as $filter){
            $data = $filter->execute($data);
        }

        return $data;
    }
}
