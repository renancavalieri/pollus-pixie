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

namespace Pollus\Pixie;

use Pollus\Pixie\ConnectionAdapters\IConnectionAdapter;
use Pollus\Pixie\Event\EventHandler;
use Pollus\Pixie\QueryBuilder\QueryBuilderHandler;
use Pollus\Pixie\QueryBuilder\QueryObject;

/**
 * Class Connection
 *
 * @package Pollus\Pixie
 */
class Connection
{
    /**
     * Connection adapter (i.e. Mysql, Pgsql, Sqlite)
     *
     * @var IConnectionAdapter
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $adapterConfig;

    /**
     * @var \PDO
     */
    protected $pdoInstance;

    /**
     * @var EventHandler
     */
    protected $eventHandler;

    /**
     * @var QueryObject|null
     */
    protected $lastQuery;

    /**
     * @param string $adapter Adapter name or class
     * @param array $adapterConfig
     */
    public function __construct($adapter, array $adapterConfig)
    {
        if (($adapter instanceof IConnectionAdapter) === false) {
            /* @var $adapter IConnectionAdapter */
            $adapter = '\Pollus\Pixie\ConnectionAdapters\\' . ucfirst(strtolower($adapter));
            $adapter = new $adapter();
        }

        $this
            ->setAdapter($adapter)
            ->setAdapterConfig($adapterConfig);

        // Create event dependency
        $this->eventHandler = new EventHandler();
    }

    /**
     * Create the connection adapter and connect to database
     * @return static
     */
    public function connect(): self
    {
        if ($this->pdoInstance !== null) {
            return $this;
        }

        // Build a database connection if we don't have one connected
        $this->setPdoInstance($this->getAdapter()->connect($this->getAdapterConfig()));

        return $this;
    }

    /**
     * @return IConnectionAdapter
     */
    public function getAdapter(): IConnectionAdapter
    {
        return $this->adapter;
    }

    /**
     * @return array
     */
    public function getAdapterConfig(): array
    {
        return $this->adapterConfig;
    }

    /**
     * @return EventHandler
     */
    public function getEventHandler(): EventHandler
    {
        return $this->eventHandler;
    }

    /**
     * @return \PDO
     */
    public function getPdoInstance(): \PDO
    {
        if ($this->pdoInstance === null)
        {
            $this->connect();
        }
        return $this->pdoInstance;
    }

    /**
     * Returns an instance of Query Builder
     *
     * @return QueryBuilderHandler
     * @throws \Pollus\Pixie\Exception
     */
    public function getQueryBuilder(): QueryBuilderHandler
    {
        return new QueryBuilderHandler($this);
    }

    /**
     * @param IConnectionAdapter $adapter
     *
     * @return static
     */
    public function setAdapter(IConnectionAdapter $adapter): self
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @param array $adapterConfig
     *
     * @return static
     */
    public function setAdapterConfig(array $adapterConfig): self
    {
        $this->adapterConfig = $adapterConfig;

        return $this;
    }

    /**
     * @param \PDO $pdo
     *
     * @return static
     */
    public function setPdoInstance(\PDO $pdo): self
    {
        $this->pdoInstance = $pdo;

        return $this;
    }

    /**
     * Set query-object for last executed query.
     *
     * @param QueryObject $query
     * @return static
     */
    public function setLastQuery(QueryObject $query): self
    {
        $this->lastQuery = $query;

        return $this;
    }

    /**
     * Get query-object from last executed query.
     *
     * @return QueryObject|null
     */
    public function getLastQuery(): ?QueryObject
    {
        return $this->lastQuery;
    }

    /**
     * Register new event
     *
     * @param string $name
     * @param string|null $table
     * @param \Closure $action
     *
     * @return void
     */
    public function registerEvent($name, $table = null, \Closure $action): void
    {
        $this->getEventHandler()->registerEvent($name, $table, $action);
    }

    /**
     * Close PDO connection
     */
    public function close(): void
    {
        $this->pdoInstance = null;
    }
    
    /**
     * Starts a transaction
     * 
     * @return bool
     */
    public function beginTransaction() : bool
    {
        return $this->adapter->beginTransaction($this->getPdoInstance());
    }
    
    /**
     * Commits a transaction
     * 
     * @return bool
     */
    public function commitTransaction() : bool
    {
        return $this->adapter->commitTransaction($this->getPdoInstance());
    }
    
    /**
     * Rollbacks a transaction
     * 
     * @return bool
     */
    public function rollbackTransaction() : bool
    {
        return $this->adapter->rollbackTransaction($this->getPdoInstance());
    }
    
    /**
     * Checks if a transaction is currently active
     * 
     * @return bool
     */
    public function inTransaction() : bool
    {
        return $this->adapter->inTransaction($this->getPdoInstance());
    }
    
    public function __destruct()
    {
        $this->close();
    }
}