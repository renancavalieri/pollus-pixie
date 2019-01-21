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
use Pollus\Pixie\Exception;

/**
 * Class Sqlite
 *
 * @package Pollus\Pixie\ConnectionAdapters
 */
class Sqlite extends BaseAdapter
{
    /**
     * @param array $config
     *
     * @return PDO
     * @throws \Pollus\Pixie\Exceptions\TableNotFoundException
     * @throws \Pollus\Pixie\Exceptions\ConnectionException
     * @throws \Pollus\Pixie\Exceptions\ColumnNotFoundException
     * @throws \Pollus\Pixie\Exception
     * @throws \Pollus\Pixie\Exceptions\DuplicateColumnException
     * @throws \Pollus\Pixie\Exceptions\DuplicateEntryException
     * @throws \Pollus\Pixie\Exceptions\DuplicateKeyException
     * @throws \Pollus\Pixie\Exceptions\ForeignKeyException
     * @throws \Pollus\Pixie\Exceptions\NotNullException
     */
    public function doConnect(array $config): PDO
    {
        if (\extension_loaded('pdo_sqlite') === false) {
            throw new Exception(sprintf('%s library not loaded', 'pdo_sqlite'));
        }

        $connectionString = 'sqlite:' . $config['database'];

        try {
            return new PDO($connectionString, null, null, $config['options']);
        } catch (\PDOException $e) {
            throw Exception::create($e, $this->getQueryAdapterClass());
        }
    }

    /**
     * Get query adapter class
     * @return string
     */
    public function getQueryAdapterClass(): string
    {
        return \Pollus\Pixie\QueryBuilder\Adapters\Sqlite::class;
    }
}