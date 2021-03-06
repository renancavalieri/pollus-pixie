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
 * Class Raw
 *
 * @package Pollus\Pixie\QueryBuilder
 */
class Raw
{

    /**
     * @var string
     */
    protected $value;

    /**
     * @var array
     */
    protected $bindings;

    /**
     * Raw constructor.
     *
     * @param string $value
     * @param array|string $bindings
     */
    public function __construct(string $value, array $bindings = [])
    {
        $this->value = $value;
        $this->bindings = $bindings;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
}