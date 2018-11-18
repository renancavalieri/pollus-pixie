<?php

namespace Pollus\Pixie;

/**
 * Connection manager for Pixie
 * 
 * This class stores all connections information and only instances when whem
 * requested by the getConnection() method. 
 * 
 * All non requested connections will not be instanced
 */
class Manager 
{
    /**
     * Instanced connections
     * 
     * @var Connection[]
     */
    protected $instances;
    
    /**
     * Supplied informations to instance the connections
     * 
     * @var array
     */
    protected $connections;
    
    
   
    /**
     * Add a new connection information to the manager, it will be instanced only
     * when getConnection() be called.
     * 
     * If the connection name already exists, it will be overwritten only if it
     * wasn't instanced yet;
     * 
     * @param array $connection
     * @param string $name
     */
    public function addConnection(array $connection, string $name = "default")    
    {
        if (isset($this->instances[$name]))
        {
            throw new ConnectionException("Cannot overwrite '$name' connection, it's already instanced");
        }
        
       $this->connections[$name] = $connection;
    }
    
    /**
     * Add a new instanced connection object to the manager.
     * 
     * @param Connection $connection
     * @param string $name
     */
    public function addConnectionInstance(Connection $connection, string $name = 'default')
    {
        $this->connections[$name] = $connection;
    }
    
    /**
     * Returns the connection instance
     * 
     * @param string $name
     */
    public function getConnection(string $name = 'default') : Connection
    {
        if (isset($this->instances[$name]))
        {
            return $this->instances[$name];
        }
        else if (isset($this->connections[$name]))
        {
            
            $this->instances[$name] = new Connection($this->connections[$name]);
            return $this->instances[$name];
        }
        throw new ConnectionException("Unknown connection: $name");
    }
}
