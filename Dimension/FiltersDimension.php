<?php

namespace Revinate\AnalyticsBundle\Dimension;

class FiltersDimension extends Dimension {

    /** @var  \Elastica\Filter\AbstractFilter[] */
    protected $filters;
    /**
     * @param $name
     * @return self
     */
    public static function create($name, $field = null) {
        return new self($name, $field);
    }

    /**
     * @param \Elastica\Filter\AbstractFilter $filter
     * @param string $name
     * @return $this
     */
    public function addFilter(\Elastica\Filter\AbstractFilter $filter, $name = null) {
        if ($name) {
            $this->filters[$name] = $filter;
        } else {
            $this->filters[] = $filter;
        }
        return $this;
    }

    /**
     * @return \Elastica\Filter\AbstractFilter[]
     */
    public function getFilters() {
        return $this->filters;
    }
}
