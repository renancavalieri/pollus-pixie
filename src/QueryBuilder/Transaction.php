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

use Pollus\Pixie\Exception;
use Pollus\Pixie\Exceptions\TransactionHaltException;

/**
 * Class Transaction
 *
 * @package Pollus\Pixie\QueryBuilder
 */
class Transaction extends QueryBuilderHandler
{

    protected $transactionStatement;

    /**
     * @param \Closure $callback
     *
     * @return static
     */
    public function transaction(\Closure $callback): Transaction
    {
        $callback($this);

        return $this;
    }

    /**
     * Commit transaction
     *
     * @throws \Pollus\Pixie\Exceptions\TableNotFoundException
     * @throws \Pollus\Pixie\Exceptions\ConnectionException
     * @throws \Pollus\Pixie\Exceptions\ColumnNotFoundException
     * @throws \Pollus\Pixie\Exception
     * @throws \Pollus\Pixie\Exceptions\DuplicateColumnException
     * @throws \Pollus\Pixie\Exceptions\DuplicateEntryException
     * @throws \Pollus\Pixie\Exceptions\DuplicateKeyException
     * @throws \Pollus\Pixie\Exceptions\ForeignKeyException
     * @throws \Pollus\Pixie\Exceptions\NotNullException
     * @throws TransactionHaltException
     */
    public function commit() : void
    {
        try {
            $this->pdo()->commit();
        } catch (\PDOException $e) {
            throw Exception::create($e, $this->getConnection()->getAdapter()->getQueryAdapterClass(), $this->getLastQuery());
        }

        throw new TransactionHaltException('Commit triggered transaction-halt.');
    }

    /**
     * Rollback transaction
     *
     * @throws \Pollus\Pixie\Exceptions\TableNotFoundException
     * @throws \Pollus\Pixie\Exceptions\ConnectionException
     * @throws \Pollus\Pixie\Exceptions\ColumnNotFoundException
     * @throws \Pollus\Pixie\Exception
     * @throws \Pollus\Pixie\Exceptions\DuplicateColumnException
     * @throws \Pollus\Pixie\Exceptions\DuplicateEntryException
     * @throws \Pollus\Pixie\Exceptions\DuplicateKeyException
     * @throws \Pollus\Pixie\Exceptions\ForeignKeyException
     * @throws \Pollus\Pixie\Exceptions\NotNullException
     * @throws TransactionHaltException
     */
    public function rollBack() : void
    {
        try {
            $this->pdo()->rollBack();
        } catch (\PDOException $e) {
            throw Exception::create($e, $this->getConnection()->getAdapter()->getQueryAdapterClass(), $this->getLastQuery());
        }

        throw new TransactionHaltException('Rollback triggered transaction-halt.');
    }

    /**
     * Execute statement
     *
     * @param string $sql
     * @param array $bindings
     *
     * @return array PDOStatement and execution time as float
     * @throws \Pollus\Pixie\Exceptions\TableNotFoundException
     * @throws \Pollus\Pixie\Exceptions\ConnectionException
     * @throws \Pollus\Pixie\Exceptions\ColumnNotFoundException
     * @throws \Pollus\Pixie\Exception
     * @throws \Pollus\Pixie\Exceptions\DuplicateColumnException
     * @throws \Pollus\Pixie\Exceptions\DuplicateEntryException
     * @throws \Pollus\Pixie\Exceptions\DuplicateKeyException
     * @throws \Pollus\Pixie\Exceptions\ForeignKeyException
     * @throws \Pollus\Pixie\Exceptions\NotNullException
     * @throws Exception
     */
    public function statement(string $sql, array $bindings = []): array
    {
        if ($this->transactionStatement === null && $this->pdo()->inTransaction() === true) {

            $results = parent::statement($sql, $bindings);
            $this->transactionStatement = $results[0];

            return $results;
        }

        return parent::statement($sql, $bindings);
    }

}