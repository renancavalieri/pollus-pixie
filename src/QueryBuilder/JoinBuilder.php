<?php

/**
 * Pixie
 * @license https://opensource.org/licenses/MIT MIT
 * @author Renan Cavalieri <renan@tecdicas.com>
 * 
 * Forked from:
 *  {@see https://github.com/skipperbent/pecee-pixie skipperbent/pecee-pixie}
 *  {@see https://github.com/usmanhalalit/pixie usmanhalalit/pixie}
 */

namespace Pollus\Pixie\QueryBuilder;

/**
 * Class JoinBuilder
 *
 * @package Pollus\Pixie\QueryBuilder
 */
class JoinBuilder extends QueryBuilderHandler
{
    /**
     * Add join
     *
     * @param string|Raw|\Closure $key
     * @param string|Raw|\Closure $operator
     * @param string|Raw|\Closure $value
     * @param string $joiner
     *
     * @return static
     */
    public function on($key, $operator, $value, $joiner = 'AND'): self
    {
        $this->statements['criteria'][] = [
            'key'       => $this->addTablePrefix($key),
            'operator'  => $operator,
            'value'     => $this->addTablePrefix($value),
            'joiner'    => $joiner,
            'condition' => 'ON',
        ];

        return $this;
    }

    /**
     * Add join with USING syntax
     *
     * @param array $columns
     * @return static
     */
    public function using(array $columns): self
    {
        $this->statements['criteria'][] = [
            'columns' => $this->addTablePrefix($columns),
            'joiner'  => 'AND USING',
        ];

        return $this;
    }

    /**
     * Add OR ON join
     *
     * @param string|Raw|\Closure $key
     * @param string|Raw|\Closure $operator
     * @param string|Raw|\Closure $value
     *
     * @return static
     */
    public function orOn($key, $operator, $value): self
    {
        return $this->on($key, $operator, $value, 'OR');
    }

}