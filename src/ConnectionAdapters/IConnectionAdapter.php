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

namespace Pollus\Pixie\ConnectionAdapters;

use PDO;

interface IConnectionAdapter
{

    /**
     * Connect to database
     *
     * @param array $config
     *
     * @return PDO
     */
    public function connect(array $config): PDO;

    /**
     * Get query adapter class
     * @return string
     */
    public function getQueryAdapterClass(): string;
    
    /**
     * Starts a transaction
     * @param PDO $pdo
     * @return bool
     */
    public function beginTransaction(PDO $pdo) : bool;
    
    /**
     * Commits a transaction
     * 
     * @param PDO $pdo
     * @return bool
     */
    public function commitTransaction(PDO $pdo) : bool;
    
    /**
     * Rollbacks a transaction
     * 
     * @param PDO $pdo
     * @return bool
     */
    public function rollbackTransaction(PDO $pdo) : bool;
    
    /**
     * Checks if a transaction is currently active
     * 
     * @param PDO $pdo
     * @return bool
     */
    public function inTransaction(PDO $pdo) : bool;
}