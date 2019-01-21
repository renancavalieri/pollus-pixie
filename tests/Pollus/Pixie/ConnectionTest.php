<?php namespace Pollus\Pixie;

use Mockery as m;
use Pollus\Pixie\ConnectionAdapters\IConnectionAdapter;
use Pollus\Pixie\ConnectionAdapters\Mysql;
use Pollus\Pixie\QueryBuilder\QueryBuilderHandler;

/**
 * Class ConnectionTest
 *
 * @package Pollus\Pixie
 */
class ConnectionTest extends TestCase
{
    /**
     * @var \Mockery\Mock
     */
    private $mysqlConnectionMock;
    /**
     * @var \Pollus\Pixie\Connection
     */
    private $connection;

    /**
     * @var QueryBuilderHandler
     */
    protected $builder;

    public function setUp()
    {
        parent::setUp();

        $this->mysqlConnectionMock = m::mock(Mysql::class);
        $this->mysqlConnectionMock->shouldReceive('connect')->andReturn($this->mockPdo);

        $this->connection = new Connection($this->mysqlConnectionMock, ['prefix' => 'cb_']);
    }

    public function testConnection()
    {
        $this->connection->connect();
        $this->assertEquals($this->mockPdo, $this->connection->getPdoInstance());
        $this->assertInstanceOf(\PDO::class, $this->connection->getPdoInstance());
        $this->assertInstanceOf(IConnectionAdapter::class, $this->connection->getAdapter());
        $this->assertEquals(['prefix' => 'cb_'], $this->connection->getAdapterConfig());
    }

    /**
     * Test multiple connections
     * @throws Exception
     */
    public function testMultiConnection()
    {
        $mysqlMock = m::mock(Mysql::class);
        $mysqlMock->shouldReceive('connect')->andReturn($this->mockPdo);
        $mysqlMock->shouldReceive('getQueryAdapterClass')->andReturn(\Pollus\Pixie\QueryBuilder\Adapters\Mysql::class);

        $connectionOneHost = 'google.com';
        $connectionTwoHost = 'yahoo.com';

        $connectionOne = new Connection($mysqlMock, [
            'database' => 'db',
            'username' => 'username',
            'password' => 'password',
            'host'     => $connectionOneHost,
        ]);

        $connectionTwo = new Connection($mysqlMock, [
            'database' => 'db',
            'username' => 'username',
            'password' => 'password',
            'host'     => $connectionTwoHost,
        ]);

        $adapterConfigOne = $connectionOne
            ->getQueryBuilder()
            ->getConnection()
            ->getAdapterConfig();

        $adapterConfigTwo = $connectionTwo
            ->getQueryBuilder()
            ->getConnection()
            ->getAdapterConfig();

        $this->assertEquals($adapterConfigOne['host'], $connectionOneHost);
        $this->assertEquals($adapterConfigTwo['host'], $connectionTwoHost);
    }
}
