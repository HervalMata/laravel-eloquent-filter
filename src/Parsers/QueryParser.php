<?php

namespace Mnabialek\LaravelEloquentFilter\Parsers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class QueryParser
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Ignore filters with empty values
     *
     * @var bool
     */
    protected $ignoreEmptyFilters = false;

    /**
     * Filters that should be ignored
     *
     * @var array
     */
    protected $ignoredFilters = [];

    /**
     * Filter operator
     *
     * @var string
     */
    protected $filterOperator = '=';

    /**
     * QueryParser constructor.
     *
     * @param Request $request
     * @param Collection $collection
     */
    public function __construct(Request $request, Collection $collection)
    {
        $this->request = $request;
        $this->collection = $collection;
    }

    /**
     * Add filter to ignored
     *
     * @param $field
     */
    protected function addIgnoredFilter($field)
    {
        $this->ignoredFilters[] = $field;
    }

    /**
     * Get ignored filters
     *
     * @return array
     */
    protected function getIgnoredFilters()
    {
        return $this->ignoredFilters;
    }

    /**
     * Get filter operator
     *
     * @return string
     */
    protected function getFilterOperator()
    {
        return $this->filterOperator;
    }

    /**
     * Filter empty values from collection if empty filtering is enabled
     *
     * @param Collection $input
     *
     * @return Collection
     */
    protected function filterEmptyValues(Collection $input)
    {
        return !$this->ignoreEmptyFilters ? $input :
            $input->reject(function ($value) {
                return empty($value);
            });
    }
}