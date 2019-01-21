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

namespace Pollus\Pixie\Event;

use Pollus\Pixie\QueryBuilder\QueryBuilderHandler;
use Pollus\Pixie\QueryBuilder\QueryObject;
use Pollus\Pixie\QueryBuilder\Raw;

/**
 * Class EventHandler
 *
 * @package Pollus\Pixie
 */
class EventHandler
{

    /**
     * Event-type that fires before each query
     *
     * @var string
     */
    public const EVENT_BEFORE_ALL = 'before-*';

    /**
     * Event-type that fires after each query
     *
     * @var string
     */
    public const EVENT_AFTER_ALL = 'after-*';

    /**
     * This event fires before a custom query
     *
     * @var string
     */
    public const EVENT_BEFORE_QUERY = 'before-query';

    /**
     * This event fires after a custom query
     *
     * @var string
     */
    public const EVENT_AFTER_QUERY = 'after-query';

    /**
     * Event-type that fires before select
     *
     * @var string
     */
    public const EVENT_BEFORE_SELECT = 'before-select';

    /**
     * Event-type that fires after select
     *
     * @var string
     */
    public const EVENT_AFTER_SELECT = 'after-select';

    /**
     * Event-type that fires before insert
     *
     * @var string
     */
    public const EVENT_BEFORE_INSERT = 'before-insert';

    /**
     * Event-type that fires after insert
     *
     * @var string
     */
    public const EVENT_AFTER_INSERT = 'after-insert';

    /**
     * Event-type that fires before update
     *
     * @var string
     */
    public const EVENT_BEFORE_UPDATE = 'before-update';

    /**
     * Event-type that fires after update
     *
     * @var string
     */
    public const EVENT_AFTER_UPDATE = 'after-update';

    /**
     * Event-type that fires before delete
     *
     * @var string
     */
    public const EVENT_BEFORE_DELETE = 'before-delete';

    /**
     * Event-type that fires after delete
     *
     * @var string
     */
    public const EVENT_AFTER_DELETE = 'after-delete';

    /**
     * Fake table name for any table events
     */
    public const TABLE_ANY = ':any';
    /**
     * @var array
     */
    protected $events = [];

    /**
     * @param string $name
     * @param QueryObject $queryObject
     * @param QueryBuilderHandler $queryBuilder
     * @param array $eventArguments
     * @return void
     */
    public function fireEvents(string $name, QueryObject $queryObject, QueryBuilderHandler $queryBuilder, array $eventArguments = []): void
    {
        $statements = $queryBuilder->getStatements();
        $tables = $statements['tables'] ?? [];

        // Events added with :any will be fired in case of any table, we are adding :any as a fake table at the beginning.
        array_unshift($tables, self::TABLE_ANY);

        // Fire all events
        foreach ($tables as $table) {
            // Fire before events for :any table
            $action = $this->getEvent($name, $table);
            if ($action === null) {
                continue;
            }

            $action(new EventArguments($name, $queryObject, $queryBuilder, $eventArguments));
        }
    }

    /**
     * @param string $event
     * @param string|Raw|null $table
     *
     * @return callable|null
     */
    public function getEvent(string $event, ?string $table = null): ?callable
    {
        $table = $table ?? self::TABLE_ANY;

        if ($table instanceof Raw) {
            return null;
        }

        if (isset($this->events[$table][$event]) === true) {
            return $this->events[$table][$event];
        }

        // Find event with wildcard (*)
        if (isset($this->events[$table]) === true) {
            foreach ((array)$this->events[$table] as $name => $e) {
                if (strpos($name, '*') !== false) {
                    $name = substr($name, 0, strpos($name, '*'));
                    if (strpos($event, $name) !== false) {
                        return $e;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param string $event
     * @param string|null $table
     * @param \Closure $action
     *
     * @return void
     */
    public function registerEvent(string $event, ?string $table = null, \Closure $action): void
    {
        $this->events[$table ?? self::TABLE_ANY][$event] = $action;
    }

    /**
     * @param string $event
     * @param string $table
     *
     * @return void
     */
    public function removeEvent($event, $table = null): void
    {
        unset($this->events[$table ?? self::TABLE_ANY][$event]);
    }
}