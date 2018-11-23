<?php

namespace Pollus\Pixie\QueryBuilder;

use Pollus\Pixie\QueryBuilder\QueryBuilderHandler;

/**
 * Class NestedCriteria
 *
 * @package Pollus\Pixie\QueryBuilder
 */
class NestedCriteria extends QueryBuilderHandler
{
    /**
     * @param string|Raw|\Closure $key
     * @param string|Raw|\Closure|null $operator
     * @param string|Raw|\Closure|null $value
     * @param string $joiner
     *
     * @return static
     */
    protected function whereHandler($key, string $operator = null, $value = null, $joiner = 'AND'): QueryBuilderHandler
    {
        $key = $this->addTablePrefix($key);
        $this->statements['criteria'][] = compact('key', 'operator', 'value', 'joiner');

        return $this;
    }
}