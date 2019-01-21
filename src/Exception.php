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

use Pollus\Pixie\Exceptions\ColumnNotFoundException;
use Pollus\Pixie\Exceptions\ConnectionException;
use Pollus\Pixie\Exceptions\DuplicateColumnException;
use Pollus\Pixie\Exceptions\DuplicateEntryException;
use Pollus\Pixie\Exceptions\DuplicateKeyException;
use Pollus\Pixie\Exceptions\ForeignKeyException;
use Pollus\Pixie\Exceptions\NotNullException;
use Pollus\Pixie\Exceptions\TableNotFoundException;
use Pollus\Pixie\QueryBuilder\Adapters\Mysql;
use Pollus\Pixie\QueryBuilder\Adapters\Sqlite;
use Pollus\Pixie\QueryBuilder\QueryObject;
use Throwable;

/**
 * Class Exception
 *
 * @package Pollus\Pixie
 */
class Exception extends \Exception
{
    protected $query;

    public function __construct(string $message = '', int $code = 0, Throwable $previous = null, QueryObject $query = null)
    {
        parent::__construct($message, $code, $previous);
        $this->query = $query;
    }

    /**
     * @param \Exception $e
     * @param string|null $adapterName
     * @param \Pollus\Pixie\QueryBuilder\QueryObject|null $query
     *
     * @return static|ColumnNotFoundException|ConnectionException|DuplicateColumnException|DuplicateEntryException|DuplicateKeyException|ForeignKeyException|NotNullException|TableNotFoundException
     *
     * @see https://dev.mysql.com/doc/refman/5.6/en/error-messages-server.html
     * @see https://www.postgresql.org/docs/9.4/static/errcodes-appendix.html
     * @see https://sqlite.org/c3ref/c_abort.html
     */
    public static function create(\Exception $e, string $adapterName = null, QueryObject $query = null)
    {

        if ($e instanceof \PDOException) {

            /**
             * @var string|null $errorSqlState
             * @var integer|null $errorCode
             * @var string|null $errorMsg
             */
            [$errorSqlState, $errorCode, $errorMsg] = $e->errorInfo;

            $errorMsg = $errorMsg ?? $e->getMessage();
            $errorCode = (int)($errorCode ?? $e->getCode());

            switch ($adapterName) {
                case Mysql::class:
                    // https://dev.mysql.com/doc/refman/5.6/en/error-messages-server.html
                    switch ($errorCode) {
                        case 1062: // Message: Duplicate entry '%s' for key %d
                            return new DuplicateEntryException($errorMsg, $errorCode, $e->getPrevious(), $query);
                        case 1451: // Message: Cannot delete or update a parent row: a foreign key constraint fails (%s)
                        case 1452: // Message: Cannot add or update a child row: a foreign key constraint fails (%s)
                            return new ForeignKeyException($errorMsg, $errorCode, $e->getPrevious(), $query);
                        case 1048: // Column '%s' cannot be null
                            return new NotNullException($errorMsg, $errorCode, $e->getPrevious(), $query);
                        case 2013: // lost connection
                        case 2005: // unknown server host
                        case 1045: // access denied
                        case 1044: // access denied
                        case 2002: // failed to connect to server
                            return new ConnectionException($errorMsg, $errorCode, $e->getPrevious(), $query);
                        case 1146: // table doesn't exist
                            return new TableNotFoundException($errorMsg, $errorCode, $e->getPrevious(), $query);
                        case 1054: // unknown column
                            return new ColumnNotFoundException($errorMsg, $errorCode, $e->getPrevious(), $query);
                        case 1060: // Duplicate column name '%s'
                            return new DuplicateColumnException($errorMsg, $errorCode, $e->getPrevious(), $query);
                        case 1061: // Message: Duplicate key name '%s'
                            return new DuplicateKeyException($errorMsg, $errorCode, $e->getPrevious(), $query);
                    }
                    break;
                case Sqlite::class:
                    // https://sqlite.org/c3ref/c_abort.html
                    /**
                     * Hack for SQLite3 exceptions.
                     * Error messages from source code: https://www.sqlite.org/download.html
                     */
                    switch ($errorSqlState) {
                        case null;
                            if ($errorCode === 14) {
                                return new ConnectionException($errorMsg, 1, $e->getPrevious(), $query);
                            }
                            break;
                        case 'HY000':
                        case '23000':
                            if (preg_match('/no such column:/', $errorMsg) === 1) {
                                return new ColumnNotFoundException($errorMsg, 1, $e->getPrevious(), $query);
                            }
                            if (preg_match('/no such table:/', $errorMsg) === 1) {
                                return new TableNotFoundException($errorMsg, 1, $e->getPrevious(), $query);
                            }
                            if (preg_match('/NOT NULL constraint failed:/', $errorMsg) === 1) {
                                return new NotNullException($errorMsg, 1, $e->getPrevious(), $query);
                            }
                            if (preg_match('/UNIQUE constraint failed:/', $errorMsg) === 1) {
                                return new DuplicateEntryException($errorMsg, 1, $e->getPrevious(), $query);
                            }
                            if (preg_match('/FOREIGN KEY constraint failed/', $errorMsg) === 1) {
                                return new ForeignKeyException($errorMsg, 1, $e->getPrevious(), $query);
                            }
                            break;
                    }
                    break;
            }
        }

        return new static($e->getMessage(), (int)$e->getCode(), $e->getPrevious(), $query);
    }

    /**
     * Get query-object from last executed query.
     *
     * @return QueryObject|null
     */
    public function getQuery(): ?QueryObject
    {
        return $this->query;
    }
}
