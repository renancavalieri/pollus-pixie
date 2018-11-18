<?php

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
     * Connection adapter (i.e. Mysql)
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
     * Initializes a new connection.
     * 
     * When none adapter is supplied, the default 'mysql' is used.
     * 
     * 
     * @param array $adapterConfig
     */
    public function __construct(array $adapterConfig)
    {
        $adapter = $adapterConfig["adapter"] ?? 'mysql';
        
        if (($adapter instanceof IConnectionAdapter) === false) 
        {
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
     * 
     * @return Connection
     */
    public function connect(): Connection
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
     * @return Connection;
     */
    public function setAdapter(IConnectionAdapter $adapter): Connection
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @param array $adapterConfig
     *
     * @return Connection
     */
    public function setAdapterConfig(array $adapterConfig): Connection
    {
        $this->adapterConfig = $adapterConfig;

        return $this;
    }

    /**
     * @param \PDO $pdo
     *
     * @return Connection
     */
    public function setPdoInstance(\PDO $pdo): Connection
    {
        $this->pdoInstance = $pdo;

        return $this;
    }

    /**
     * Set query-object for last executed query.
     *
     * @param QueryObject $query
     * 
     * @return Connection
     */
    public function setLastQuery(QueryObject $query): Connection
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
     * @return Connection
     */
    public function registerEvent($name, $table = null, \Closure $action): Connection
    {
        $this->getEventHandler()->registerEvent($name, $table, $action);
        return $this;
    }

    /**
     * Close PDO connection
     */
    public function close(): void
    {
        $this->pdoInstance = null;
    }
    
    
    /**
     * Start the transaction
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connect()
            ->getPdoInstance()
            ->beginTransaction();
    }
    
    /**
     * Rollback the transaction
     * return @bool
     */
    public function rollback()
    {
        return $this->connect()
            ->getPdoInstance()
            ->rollBack();
    }
    
    /**
     * Commit the transaction
     * 
     * return @bool
     */
    public function commit()
    {
        return $this->connect()
            ->getPdoInstance()
            ->commit();
    }
    
    /**
     * Returns whether is in transaction or not
     * 
     * @return bool
     */
    public function inTransaction() : bool
    {
        return $this->connect()
                ->getPdoInstance()
                ->inTransaction();
    }
    
    /**
     * Alias for getQueryBuilder();
     * 
     * @return QueryBuilderHandler
     */
    public function newQuery() : QueryBuilderHandler
    {
        return $this->getQueryBuilder();
    }
    
    
    public function __destruct()
    {
        $this->close();
    }

}