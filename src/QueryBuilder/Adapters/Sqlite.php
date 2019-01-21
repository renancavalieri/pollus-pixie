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

namespace Pollus\Pixie\QueryBuilder\Adapters;

/**
 * Class Sqlite
 *
 * @package Pollus\Pixie\QueryBuilder\Adapters
 */
class Sqlite extends BaseAdapter
{
    /**
     * @var string
     */
    public const SANITIZER = '`';
}
