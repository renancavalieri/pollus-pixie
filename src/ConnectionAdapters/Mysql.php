<?php

namespace Pollus\Pixie\ConnectionAdapters;

use PDO;
use Pollus\Pixie\Exception;

/**
 * Class Mysql
 *
 * @package Pollus\Pixie\ConnectionAdapters
 */
class Mysql extends BaseAdapter
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
    protected function doConnect(array $config): PDO
    {
        if (\extension_loaded('pdo_mysql') === false) {
            throw new Exception(sprintf('%s library not loaded', 'pdo_mysql'));
        }

        $connectionString = "mysql:dbname={$config['database']}";

        if (isset($config['host']) === true) {
            $connectionString .= ";host={$config['host']}";
        }

        if (isset($config['port']) === true) {
            $connectionString .= ";port={$config['port']}";
        }

        if (isset($config['unix_socket']) === true) {
            $connectionString .= ";unix_socket={$config['unix_socket']}";
        }

        try {

            $connection = new PDO($connectionString, $config['username'], $config['password'], $config['options']);

            if (isset($config['charset']) === true) {
                $connection->prepare("SET NAMES '{$config['charset']}'")->execute();
            }

        } catch (\PDOException $e) {
            throw Exception::create($e, $this->getQueryAdapterClass());
        }

        return $connection;
    }

    /**
     * Get query adapter class
     * @return string
     */
    public function getQueryAdapterClass(): string
    {
        return \Pollus\Pixie\QueryBuilder\Adapters\Mysql::class;
    }
}