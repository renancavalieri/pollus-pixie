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

/**
 * Class BaseAdapter
 *
 * @package Pollus\Pixie\ConnectionAdapters
 */
abstract class BaseAdapter implements IConnectionAdapter
{
    /**
     * @param array $config
     *
     * @return PDO
     */
    public function connect(array $config): PDO
    {
        if (isset($config['options']) === false) {
            $config['options'] = [];
        }

        return $this->doConnect($config);
    }

    /**
     * @param array $config
     *
     * @return PDO
     */
    abstract protected function doConnect(array $config): PDO;
    
    
    /**
     * Starts a transaction
     * @param PDO $pdo
     * @return bool
     */
    public function beginTransaction(PDO $pdo) : bool
    {
        return $pdo->beginTransaction();
    }
    
    /**
     * Commits a transaction
     * 
     * @param PDO $pdo
     * @return bool
     */
    public function commitTransaction(PDO $pdo) : bool
    {
        return $pdo->commit();
    }
    
    /**
     * Rollbacks a transaction
     * 
     * @param PDO $pdo
     * @return bool
     */
    public function rollbackTransaction(PDO $pdo) : bool
    {
        return $pdo->rollBack();
    }
    
    /**
     * Checks if a transaction is currently active
     * 
     * @param PDO $pdo
     * @return bool
     */
    public function inTransaction(PDO $pdo) : bool
    {
        return $pdo->inTransaction();
    }
}