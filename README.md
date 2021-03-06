# pollus/pixie: Lightweight query builder

This is a **forked version** of pecee/pixie 4.11.0 running on different namespace. All the credits goes to [skipperbent](https://github.com/skipperbent/pecee-pixie) and [usmanhalalit](https://github.com/usmanhalalit/pixie).

## Major changes

**New in 6.1.0**
 - Added `filter` and `orFilter` methods.

**New in 6.0.0**
 - Added **findOrFail()** method, which will throw an exception if the query returns NULL.
 - Added **save()** method, that will execute an UPDATE on a record if the primary key is present, otherwise an INSERT will be performed.
 - Added locking rows support for pessimistic strategy (lockForUpdate() and sharedLock()) **(MySQL only)**
 - Dropped support to PostgreSQL

**New in 5.0.0**:
 - Simple Connection Manager (based on Capsule\Manager from Laravel), allowing multiple connections management.
 - Removed **IQueryBuilderHandler** marker interface to allow IDEs like NetBeans autocomplete the query.
 - Removed static reference to connection on QueryBuilderHandler
 - Transaction methods can be accessed on Connection object

**Why a fork?**

Because I needed these features and had no time to propose it to the originals developers. I can collaborate with the original project and help implement these new features (**if the author wants**).

## New features example

```php
// Using the connection manager to store multiple connections
$databases = array
(
    'default' =>
    [
        'adapter'   => 'mysql' // when NULL, driver name will be used as adapter
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'my_table',
        'username'  => 'root',
        'password'  => 'p4ssw0rd!',
        'charset'   => 'utf8',
        //'collation' => 'utf8_unicode_ci',     // optional
        //'prefix'    => '',                    // optional
    ],

    'session_mock' => 
    [
        'driver'   => 'sqlite',
        'database' => ':memory:',
        //'database' => __DIR__.'/db_sqlite.sqlite',
        'prefix'   => '',
    ],
    
    'sessions' =>
    [
        'driver'    => 'mysql',
        'host'      => '192.168.2.2',
        'database'  => 'sessions',
        'username'  => 'root',
        'password'  => 'p4ssw0rd!',
        'charset'   => 'utf8',
        //'collation' => 'utf8_unicode_ci',     // optional
        //'prefix'    => '',                    // optional
    ],
);


$db = new \Pollus\Pixie\Manager();

foreach ($databases as $name => $db_conf) 
{
    $db->addConnection($db_conf, $name);
}

// but only "sessions" connection was instanced, because the method getConnection was invoked.
$con = $db->getConnection('sessions');

// [...] later in the code, using pessimistic locking inside a transaction
$con->getQueryBuilder()
    ->table('user_sessions')
    ->select('*')
    ->lockForUpdate()
    ->first();
```

## Documentation

A lightweight, expressive, framework agnostic query builder for PHP it can also be referred as a Database Abstraction Layer.

Pollus/Pixie supports MySQL and SQLite and will handle all your query sanitization, table alias, unions among many other things, with a unified API.

The syntax is similar to Laravel's query builder "Eloquent", but with less overhead.

**Requirements:**
- PHP version 7.2 or higher is required for pollus-pixie version 6.x and above.
- PDO extension enabled.

**Features:**

- Connection manager.
- Improved sub-queries.
- Custom prefix/aliases for tables (prefix.`table`).
- Support for not defining table and/or removing defined table.
- Better handling of `Raw` objects in `where` statements.
- Union queries.
- Better connection handling.
- Performance optimisations.
- Tons of bug fixes.
- Query events
- Nested criteria
- Sub queries
- Nested queries
- Multiple database connections.

### Examples
```php
// Make sure you have Composer's autoload file included
require 'vendor/autoload.php';

// Create a connection, once only.
$config =
[
    // Name of database driver or IConnectionAdapter class
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'your-database',
    'username'  => 'root',
    'password'  => 'your-password',

    // Optional
    'charset'   => 'utf8',

    // Optional
    'collation' => 'utf8_unicode_ci',

    // Table prefix, optional
    'prefix'    => 'cb_',

    // PDO constructor options, optional
    'options'   => [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];

$queryBuilder = (new \Pollus\Pixie\Connection('mysql', $config))->getQueryBuilder();
```

**Simple query:**

Get user with the id of `3`. Returns `null` when no match.

```php
$user = $queryBuilder
            ->table('users')
            ->find(3);
```

**Full queries:**

Get all users with blue or red hair.

```php
$users = $queryBuilder
            ->table('users')
            ->where('hair_color', '=', 'blue')
            ->orWhere('hair_color', '=', 'red')
            ->get();
```

___

## Table of Contents

 - [Installation](#installation)
 - [Connecting to the database](#connecting-to-the-database)
 - [**Select**](#select)
    - [Table alias](#table-alias)
    - [Get easily](#get-easily)
    - [Multiple selects](#multiple-selects)
    - [Select distinct](#select-distinct)
    - [Select from query](#select-from-query)
    - [Select single field](#select-single-field)
    - [Select multiple fields](#select-multiple-fields)
    - [Get all](#get-all)
    - [Get first row](#get-first-row)
    - [**Aggregate methods**](#aggregate-methods)
        - [Getting the row count](#getting-the-row-count)
        - [Getting the sum](#getting-the-sum)
        - [Getting the average](#getting-the-average)
        - [Getting the minimum](#getting-the-minimum)
        - [Getting the maximum](#getting-the-maximum)
    - [Selects with sub-queries](#select-with-sub-queries)
 - [**Where**](#where)
    - [Where in](#where-in)
    - [Where between](#where-between)
    - [Where null](#where-null)
    - [Grouped where](#grouped-where)
    - [Where (filter)](#filter)
 - [Group- and order by](#group--and-order-by)
 - [Having](#having)
 - [Limit and offset](#limit-and-offset)
 - [Join](#join)
    - [Join USING syntax](#join-using-syntax)
    - [Multiple join criteria](#multiple-join-criteria)
 - [Unions](#unions)
 - [Raw query](#raw-query)
    - [Raw expressions](#raw-expressions)
 - [**Insert**](#insert)
    - [Batch insert](#batch-insert)
    - [Insert with ON DUPLICATE KEY statement](#insert-with-on-duplicate-key-statement)
 - [**Update**](#update)
 - [**Delete**](#delete)
 - [Transactions](#transactions)
 - [Get raw query](#get-built-query)
    - [Get QueryObject from last executed query](#get-queryobject-from-last-executed-query)
 - [Sub-queries and nested queries](#sub-queries-and-nested-queries)
 - [Getting the PDO instance](#getting-the-pdo-instance)
 - [Fetch results as objects of specified class](#fetch-results-as-objects-of-specified-class)
 - [Advanced](#advanced)
     - [Enable query-overwriting](#enable-query-overwriting)
 - [Query events](#query-events)
    - [Available event](#available-event)
    - [Registering event](#registering-event)
    - [Removing event](#removing-event)
    - [Use cases](#use-cases)
    - [Notes](#notes)
 - [Exceptions](#exceptions)
    - [Getting sql-query from exceptions](#getting-sql-query-from-exceptions)
 - [Credits](#credits)

___

## Installation

Pixie uses [Composer](http://getcomposer.org/doc/00-intro.md#installation-nix) to make things easy.

Learn to use composer and add this to require section (in your composer.json):

```
composer install pollus/pixie
```

## Connecting to the database
Pollus/Pixie currently supports only MySQL

You can specify the driver during connection and the associated configuration when creating a new connection.

```php
// Make sure you have Composer's autoload file included
require 'vendor/autoload.php';

$config = [
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'your-database',
    'username'  => 'root',
    'password'  => 'your-password',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
];

// Creates new connection
$connection = new \Pollus\Pixie\Connection($config);

// Get the query-builder object which will initialize the database connection
$queryBuilder = $connection->getQueryBuilder();

// Run query
$person = $queryBuilder
            ->table('persons')
            ->where('name', '=', 'Bobby')
            ->first();
```

**NOTE:**
Calling the `getQueryBuilder` method will automatically make a connection to the database, if none has already established.

## Select

It is recommend to use `table()` method before every query, except raw `query()`.
To select from multiple tables just pass an array.

However this is not required.

```php
$queryBuilder->table(array('mytable1', 'mytable2'));
```

#### Table alias

You can easily set the table alias by using

```php
$queryBuilder
    ->table(['table1' => 'foo1'])
    ->join('table2', 'table2.person_id', '=', 'foo1.id');
```

You can change the alias anytime by using:

```php
$queryBuilder->alias('foo1', 'table1');

// Simplified way...

$queryBuilder->table('table1')->alias('foo1');
```

Output:

```sql
SELECT *
FROM `table1` AS `foo1`
INNER JOIN `cb_table2` ON `cb_table2`.`person_id` = `cb_foo1`.`id`
```

**NOTE:**
You can always remove a table from a query by calling the `table` method with no arguments like this `$queryBuilder->table()`.

#### Get easily

The query below returns the (first) row where id = 3, null if no rows.

```php
$row = $queryBuilder
            ->table('my_table')
            ->find(3);
```

You also can use the method `findOrFail`, which performs a `find`, however it will throw an FindException if no results were found.

```php
$row = $queryBuilder
            ->table('my_table')
            ->findOrFail(3);
```

Access your row like, `echo $row->name`. If your field name is not `id` then pass the field name as second parameter `$queryBuilder->table('my_table')->find(3, 'person_id');`.

The query below returns the all rows where name = 'Sana', null if no rows.

```php
$result = $queryBuilder
            ->table('my_table')
            ->findAll('name', 'Sana');
```

#### Multiple selects

```php
$queryBuilder
    ->select(
        [
            'mytable.myfield1',
            'mytable.myfield2',
            'another_table.myfield3'
        ]
    );
```

Using select method multiple times `select('a')->select('b')` will also select `a` and `b`. Can be useful if you want to do conditional selects (within a PHP `if`).

#### Select distinct

```php
$queryBuilder->selectDistinct(array('mytable.myfield1', 'mytable.myfield2'));
```

#### Select from query

You can easily select items from another query by using

```php
$subQuery = $queryBuilder->table('person');
$builder = $queryBuilder->table($queryBuilder->subQuery($subQuery))->where('id', '=', 2);
```

Will produce the following output:

```sql
SELECT * FROM (SELECT * FROM `person`) WHERE `id` = 2
```

#### Select single field

```php
$queryBuilder->table('my_table')->select('*');
```

#### Select multiple fields

```php
$queryBuilder->table('my_table')->select(array('field1', 'field2'));
```

#### Get all

Returns an array.

```php
$results = $queryBuilder
                ->table('my_table')
                ->where('name', '=', 'Sana')
                ->get();
```

You can loop through it like:

```php
foreach ($results as $row) {
    echo $row->name;
}
```

#### Get first row

```php
$row = $queryBuilder
            ->table('my_table')
            ->where('name', '=', 'Sana')
            ->first();
```
Returns the first row, or null if there is no record. Using this method you can also make sure if a record exists. Access these like `echo $row->name`.

#### Aggregate methods

##### Getting the row count

This will return the count for the entire number of rows in the result.

The default behavior will count `*` (all) fields. You can specify a custom field by changing the `field` parameter.

```php
$queryBuilder
    ->table('my_table')
    ->where('name', '=', 'Sana')
    ->count();
```

##### Getting the sum

This will return the sum for a field in the entire number of rows in the result.

```php
$queryBuilder
    ->table('my_table')
    ->where('name', '=', 'Sana')
    ->sum('views');
```

##### Getting the average

This will return the average for a field in the entire number of rows in the result.

```php
$queryBuilder
    ->table('my_table')
    ->where('name', '=', 'Sana')
    ->average('views');
```

##### Getting the minimum

This will return the minimum for a field in the entire number of rows in the result.

```php
$queryBuilder
    ->table('my_table')
    ->where('name', '=', 'Sana')
    ->min('views');
```

##### Getting the maximum

This will return the average for a field in the entire number of rows in the result.

```php
$queryBuilder
    ->table('my_table')
    ->where('name', '=', 'Sana')
    ->max('views');
```

#### Select with sub-queries

```php
// Creates the first sub-query

$subQuery1 =
    $queryBuilder
        ->table('mail')
        ->select(
            $queryBuilder->raw('COUNT(*)')
        );

// Create the second sub-query

$subQuery2 =
    $queryBuilder
        ->table('event_message')
        ->select(
            $queryBuilder->raw('COUNT(*)')
        );

// Executes the query which uses the subqueries as fields
$count =
    $queryBuilder
        ->select(
            $queryBuilder->subQuery($subQuery1, 'row1'),
            $queryBuilder->subQuery($subQuery2, 'row2')
        )
        ->first();
```

The example above will output a SQL-query like this:

```sql
SELECT
  (SELECT COUNT(*)
   FROM `cb_mail`) AS row1,

  (SELECT COUNT(*)
   FROM `cb_event_message`) AS row2
LIMIT 1
```

You can also easily create a subjquery within the `where` statement:

```php
$queryBuilder->where($queryBuilder->subQuery($subQuery), '!=', 'value');
```

### Where

Basic syntax is `(fieldname, operator, value)`, if you give two parameters then `=` operator is assumed. So `where('name', 'usman')` and `where('name', '=', 'usman')` is the same.

```php
$queryBuilder
    ->table('my_table')
    ->where('name', '=', 'usman')
    ->whereNot('age', '>', 25)
    ->orWhere('type', '=', 'admin')
    ->orWhereNot('description', 'LIKE', '%query%');
```


#### Where in

```php
$queryBuilder
    ->table('my_table')
    ->whereIn('name', array('usman', 'sana'))
    ->orWhereIn('name', array('heera', 'dalim'));

$queryBuilder
    ->table('my_table')
    ->whereNotIn('name', array('heera', 'dalim'))
    ->orWhereNotIn('name', array('usman', 'sana'));
```

#### Where between

```php
$queryBuilder
    ->table('my_table')
    ->whereBetween('id', 10, 100)
    ->orWhereBetween('status', 5, 8);
```

#### Where null

```php
$queryBuilder
    ->table('my_table')
    ->whereNull('modified')
    ->orWhereNull('field2')
    ->whereNotNull('field3')
    ->orWhereNotNull('field4');
```

#### Grouped where

Sometimes queries get complex, where you need grouped criteria, for example `WHERE age = 10 and (name like '%usman%' or description LIKE '%usman%')`.

Pixie allows you to do so, you can nest as many closures as you need, like below.

```php
$queryBuilder
    ->table('my_table')
    ->where('my_table.age', 10)
    ->where(function(QueryBuilderHandler $qb) {
        $qb->where('name', 'LIKE', '%pecee%');

        // You can provide a closure on these wheres too, to nest further.
        $qb->orWhere('description', 'LIKE', '%usman%');
    });
```

### Filter

`filter` and `orFilter`methods performs in the same way that `where` and `orWhere` does, however it only takes effect if the supplied argument is not NULL.

```php
$name = null;
$age = 12;

$queryBuilder
    ->table("people")
    ->filter('name', '=', $name) // this will be ignored
    ->filter('age', '=', $age)
    ->get();
```

Is possible to use multiple OR conditions like `orWhere`

```php
$queryBuilder
    ->table("people")
    ->orFilter('name', 'Cavalieri')
    ->orFilter('age', null)
    ->get();
```

### Group- and order by

```php
$query = $queryBuilder
            ->table('my_table')
            ->groupBy('age')
            ->orderBy('created_at', 'ASC');
```

#### Multiple group by

```php
$queryBuilder
    ->groupBy(array('mytable.myfield1', 'mytable.myfield2', 'another_table.myfield3'));
    ->orderBy(array('mytable.myfield1', 'mytable.myfield2', 'another_table.myfield3'));
```

Using `groupBy()` or `orderBy()` methods multiple times `groupBy('a')->groupBy('b')` will also group by first `a` and than `b`. Can be useful if you want to do conditional grouping (within a PHP `if`). Same applies to `orderBy()`.

### Having

```php
$queryBuilder
    ->having('total_count', '>', 2)
    ->orHaving('type', '=', 'admin');
```

### Limit and offset

```php
$queryBuilder
    ->limit(30);
    ->offset(10);
```

### Join

```php
$queryBuilder
    ->table('my_table')
    ->join('another_table', 'another_table.person_id', '=', 'my_table.id')
```

Available methods,

 - join() or innerJoin
 - leftJoin()
 - rightJoin()

If you need `FULL OUTER` join or any other join, just pass it as 5th parameter of `join` method.

```php
$queryBuilder
    ->join('another_table', 'another_table.person_id', '=', 'my_table.id', 'FULL OUTER')
```

#### Join USING syntax

The `JOIN USING` syntax allows you to easily map two identical identifiers to one, which can be helpful on large queries.

Example:

```php
$queryBuilder
    ->table('user')
    ->join('user_data', 'user_data.user_id', '=', 'user.user_id');
```

Can be simplified to:

```php
$queryBuilder
    ->table('user')
    ->joinUsing('user_data', 'user_id');
```

#### Multiple join criteria

If you need more than one criterion to join a table then pass a closure as second parameter.

```php
$queryBuilder
    ->join('another_table', function($table)
    {
        $table
            ->on('another_table.person_id', '=', 'my_table.id')
            ->on('another_table.person_id2', '=', 'my_table.id2')
            ->orOn('another_table.age', '>', $queryBuilder->raw(1));
    })
```

### Unions

You can easily create unions by calling the `union` method on the `QueryBuilderHandler`.

Example:

```php
$firstQuery =
    $queryBuilder
    ->table('people')
    ->whereNull('email');

$secondQuery =
    $queryBuilder
    ->table('people')
    ->where('hair_color', '=', 'green')
    ->union($firstQuery);

$thirdQuery =
    $queryBuilder
    ->table('people')
    ->where('gender', '=', 'male')
    ->union($secondQuery);

$items = $thirdQuery->get();
```

The example above will create a sql-statement similar to this:

```sql
(
    SELECT *
    FROM
        `cb_people`
    WHERE
        `gender` = 'male'
)
UNION
(
    SELECT *
    FROM
        `cb_people`
    WHERE
        `email`
    IS NULL
)
UNION
(
    SELECT *
    FROM
        `cb_people`
    WHERE
        `hair_color` = 'green'
)
```

### Raw query

You can always perform raw queries, if needed.

```php
$query = $queryBuilder->query('SELECT * FROM persons WHERE age = 12');

$kids = $query->get();
```

You can also pass custom bindings

```php
$queryBuilder
    ->query('SELECT * FROM persons WHERE age = ? AND name = ?', array(10, 'usman'));
```

#### Raw expressions

When you wrap an expression with `raw()` method, Pixie doesn't try to sanitize these.

```php
$queryBuilder
    ->table('my_table')
    ->select($queryBuilder->raw('count(cb_my_table.id) as tot'))
    ->where('value', '=', 'Ifrah')
    ->where($queryBuilder->raw('DATE(?)', 'now'))
```


___

**NOTE:** Queries that run through `query()` method are not sanitized until you pass all values through bindings. Queries that run through `raw()` method are not sanitized either, you have to do it yourself. And of course these don't add table prefix too, but you can use the `addTablePrefix()` method.

### Insert

```php
$data = [
    'name' => 'Sana',
    'description' => 'Blah'
];

$insertId = $queryBuilder
                ->table('my_table')
                ->insert($data);
```

`insert()` method returns the insert id.

#### Batch insert

```php
$data = [
    array(
        'name'        => 'Sana',
        'description' => 'Blah'
    ),
    array(
        'name'        => 'Usman',
        'description' => 'Blah'
    ),
];

$insertIds = $queryBuilder
                ->table('my_table')
                ->insert($data);
```

In case of batch insert, it will return an array of insert ids.

#### Insert with ON DUPLICATE KEY statement

```php
$data = [
    'name'    => 'Sana',
    'counter' => 1
];

$dataUpdate = [
    'name'    => 'Sana',
    'counter' => 2
];

$insertId =
    $queryBuilder
        ->table('my_table')
        ->onDuplicateKeyUpdate($dataUpdate)
        ->insert($data);
```

### Update

```php
$data = [
    'name'        => 'Sana',
    'description' => 'Blah'
];

$queryBuilder
    ->table('my_table')
    ->where('id', 5)
    ->update($data);
```

Will update the name field to Sana and description field to Blah where id = 5.

### Delete

```php
$queryBuilder
    ->table('my_table')
    ->where('id', '>', 5)
    ->delete();
```

Will delete all the rows where id is greater than 5.

### Transactions

Pixie has the ability to run database "transactions", in which all database
changes are not saved until committed. That way, if something goes wrong or
differently then you intend, the database changes are not saved and no changes
are made.

Here's a basic transaction:

```php
$queryBuilder
    ->transaction(function (Transaction $transaction) {

        $transaction
            ->table('my_table')
            ->insert(array(
                'name' => 'Test',
                'url' => 'example.com'
            );

        $transaction
            ->table('my_table')
            ->insert(array(
                'name' => 'Test2',
                'url' => 'example.com'
            ));
});
```

If this were to cause any errors (such as a duplicate name or some other such
error), neither data set would show up in the database. If not, the changes would
be successfully saved.

If you wish to manually commit or rollback your changes, you can use the
`commit()` and `rollback()` methods accordingly:

```php
$queryBuilder
    ->transaction(function (Transaction $transaction)
        {
            $transaction
                ->table('my_table')
                ->insert($data);

            // Commit changes (data will be saved)
            $transaction->commit();

            // Rollback changes (data would be rejected)
            $transaction->rollback();
        }
    );
```

Transactions will automatically be used when inserting multiple records. For example:

```php
$queryBuilder->table('people')->insert([
    [
        'name' => 'Simon',
        'age' => 12,
        'awesome' => true,
        'nickname' => 'ponylover94',
    ],
    [
        'name' => 'Peter',
        'age' => 40,
        'awesome' => false,
        'nickname' => null,
    ],
    [
        'name' => 'Bobby',
        'age' => 20,
        'awesome' => true,
        'nickname' => 'peter',
    ],
]);
```

### Get raw query

Sometimes you may need to get the query string, it's possible.

```php
$queryHandler =
    $queryBuilder
        ->table('my_table')
        ->where('id', '=', 3)
        ->getQuery();
```

`getQuery()` will return a `QueryBuilderHandler` object.

You can use this to get the SQL, bindings or raw SQL.

```php
$queryHandler->getSql();
```

Calling `getSql()` will return the SQL-query without any processing.

```sql
SELECT * FROM my_table where `id` = ?
```

You can easily get any bindings on the query by calling the `getBindings()`method.

**Example:**

```php
$queryHandler->getBindings();
```

You can also get the raw SQL-query directly by calling the `getRawSql()` method.

**Example:**

```php
$queryHandler->getRawSql();
```

Calling the `getRawSql()` method will return a query including bindings like this.

```sql
SELECT * FROM my_table where `id` = 3
```

#### Get QueryObject from last executed query

You can also retrieve the query-object from the last executed query.

**Example:**

```php
$queryString = $queryBuilder->getLastQuery()->getRawSql();
```

### Sub-queries and nested queries

Rarely but you may need to do sub queries or nested queries. Pixie is powerful enough to do this for you. You can create different query objects and use the `$queryBuilder->subQuery()` method.

```php
$subQuery =
    $queryBuilder
        ->table('person_details')
        ->select('details')
        ->where('person_id', '=', 3);


$query =
    $queryBuilder
        ->table('my_table')
        ->select('my_table.*')
        ->select(
            $queryBuilder->subQuery($subQuery, 'table_alias1')
        );

$nestedQuery =
    $queryBuilder
        ->table(
            $queryBuilder->subQuery($query, 'table_alias2')
        )
        ->select('*');

// Execute query
$result = $nestedQuery->get();
```

This will produce a query like this:

```sql
SELECT *
FROM
  (SELECT `cb_my_table`.*,

     (SELECT `details`
      FROM `cb_person_details`
      WHERE `person_id` = 3) AS table_alias1
   FROM `cb_my_table`) AS table_alias2
```

**NOTE:**

Pixie doesn't use bindings for sub queries and nested queries. It quotes values with PDO's `quote()` method.

### Getting the PDO instance

If you need the `\PDO` instance, you can easily get it by calling:

```php
$queryBuilder->pdo();
```

If you want to get the `Connection` object you can do so like this:

```php
$connection = $queryBuilder->getConnection();
```

### Fetch results as objects of specified class

Simply call `asObject` query's method.

```php
$queryBuilder
    ->table('my_table')
    ->asObject('SomeClass', array('ctor', 'args'))
    ->first();
```

Furthermore, you may fine-tune fetching mode by calling `setFetchMode` method.

```php
$queryBuilder
    ->table('my_table')
    ->setFetchMode(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE)
    ->get();
```

### Advanced

#### Enable query-overwriting

If enabled calling from, select etc. will overwrite any existing values from previous calls in query.

You can enable or disable query-overwriting by calling the `setOverwriteEnabled` method on the `QueryBuilderHandler` object.

The feature is disabled as default.

**Example:**

```php
$queryBuilder
    ->setOverwriteEnabled(true);
```

If you want this feature to be enabled on all `QueryBuilderHandler` object as default, you can add the following setting to the connection configuration:

```php
$adapterConfig = [
    'query_overwriting' => false,
];
```

### Query events

Pixie comes with powerful query events to supercharge your application. These events are like database triggers, you can perform some actions when an event occurs, for example you can hook `after-delete` event of a table and delete related data from another table.

#### Available events

| Event constant                        | Event value/name  | Description                                           |
| :------------------------------------ | :-------------    | :------------                                         |
| `EventHandler::EVENT_BEFORE_ALL`      | `before-*`        | Event-type that fires before each query.              |
| `EventHandler::EVENT_AFTER_ALL`       | `after-*`         | Event-type that fires after each query.               |
| `EventHandler::EVENT_BEFORE_QUERY`    | `before-query`    | Event-type that fires before a raw query is executed. |
| `EventHandler::EVENT_AFTER_QUERY`     | `after-query`     | Event-type that fires after a raw query is executed   |
| `EventHandler::EVENT_BEFORE_SELECT`   | `before-select`   | Event-type that fires before select query.            |
| `EventHandler::EVENT_AFTER_SELECT`    | `after-select`    | Event-type that fires after insert query.             |
| `EventHandler::EVENT_BEFORE_INSERT`   | `before-insert`   | Event-type that fires before insert query             |
| `EventHandler::EVENT_AFTER_INSERT`    | `after-insert`    | Event-type that fires after insert query.             |
| `EventHandler::EVENT_BEFORE_UPDATE`   | `before-update`   | Event-type that fires before update query.            |
| `EventHandler::EVENT_AFTER_UPDATE`    | `after-update`    | Event-type that fires after update query.             |
| `EventHandler::EVENT_BEFORE_DELETE`   | `before-delete`   | Event-type that fires before delete query.            |
| `EventHandler::EVENT_AFTER_DELETE`    | `after-delete`    | Event-type that fires after delete query.             |

#### Registering event

You can easily register a new event either by using the `registerEvent` method on either the `QueryBuilderHandler`, `Connection` or `EventHandler` class.

The event needs a custom callback function with a `EventArguments` object as parameters.

**Examples:**

```php
$queryBuilder->registerEvent(EventHandler::EVENT_BEFORE_SELECT, 'users', function(EventArguments $arguments)
{
    $arguments
        ->getQueryBuilder()
        ->where('status', '!=', 'banned');
});
```
Now every time a select query occurs on `users` table, it will add this where criteria, so banned users don't get access.

The syntax is `registerEvent('event type', 'table name', action in a closure)`.

If you want the event to be performed when **any table is being queried**, provide `':any'` as table name.

**Other examples:**

After inserting data into `my_table`, details will be inserted into another table

```php
$queryBuilder->registerEvent(EventHandler::EVENT_AFTER_INSERT, 'my_table', function(EventArguments $arguments)
{
    $arguments
        ->getQueryBuilder()
        ->table('person_details')->insert(array(
        'person_id' => $insertId,
        'details' => 'Meh',
        'age' => 5
    ));
});
```

Whenever data is inserted into `person_details` table, set the timestamp field `created_at`, so we don't have to specify it everywhere:

```php
$queryBuilder->registerEvent(EventHandler::EVENT_AFTER_INSERT, 'person_details', function(EventArguments $arguments)
{
    $arguments
        ->getQueryBuilder()
        ->table('person_details')
        ->where('id', $insertId)
        ->update([
            'created_at' => date('Y-m-d H:i:s')
        ]);
});
```

After deleting from `my_table` delete the relations:

```php
$queryBuilder->registerEvent(EventHandler::EVENT_AFTER_DELETE, 'my_table', function(EventArguments $arguments)
{
    $bindings = $arguments->getQuery()->getBindings();

    $arguments
        ->getQueryBuilder()
        ->table('person_details')
        ->where('person_id', $binding[0])
        ->delete();
});
```

Pixie passes the current instance of query builder as first parameter of your closure so you can build queries with this object, you can do anything like usual query builder (`QB`).

If something other than `null` is returned from the `before-*` query handler, the value will be result of execution and DB will not be actually queried (and thus, corresponding `after-*` handler will not be called ether).

Only on `after-*` events you get three parameters: **first** is the query builder, **third** is the execution time as float and **the second** varies:

 - On `after-query` fires after a raw query has been executed.
 - On `after-select` you get the `results` obtained from `select`.
 - On `after-insert` you get the insert id (or array of ids in case of batch insert)
 - On `after-delete` you get the [query object](#get-built-query) (same as what you get from `getQuery()`), from it you can get SQL and Bindings.
 - On `after-update` you get the [query object](#get-built-query) like `after-delete`.

#### Removing event

```php
$queryBuilder->removeEvent($event, $table = null);
```

#### Use cases

Here are some cases where Query Events can be extremely helpful:

 - Restrict banned users.
 - Get only `deleted = 0` records.
 - Implement caching of all queries.
 - Trigger user notification after every entry.
 - Delete relationship data after a delete query.
 - Insert relationship data after an insert query.
 - Keep records of modification after each update query.
 - Add/edit created_at and updated _at data after each entry.

#### Notes
 - Query Events are set as per connection basis so multiple database connection don't create any problem, and creating new query builder instance preserves your events.
 - Query Events go recursively, for example after inserting into `table_a` your event inserts into `table_b`, now you can have another event registered with `table_b` which inserts into `table_c`.
 - Of course Query Events don't work with raw queries.

### Exceptions

 This is a list over exceptions thrown by pollus-pixie.

 All exceptions inherit from the base `Exception` class.

 | Exception name             |
 | :------------------------- |
 | `ColumnNotFoundException`  |
 | `ConnectionException`      |
 | `DuplicateColumnException` |
 | `DuplicateEntryException`  |
 | `ForeignKeyException`      |
 | `NotNullException`         |
 | `TableNotFoundException`   |
 | `Exception`                |

#### Getting sql-query from exceptions

If an error occurs and you want to debug your query - you can easily do so as all exceptions thrown by Pixie will
contain the last executed query.

You can retrieve the `QueryObject` by calling

```php
$sql = $exception->getQueryObject()->getRawSql();
```


## Licence

Licensed under the MIT licence.

### The MIT License (MIT)

Copyright (c) 2019 Renan Cavalieri

Original credits:

skipperbent: https://github.com/skipperbent/pecee-pixie
usmanhalalit: https://github.com/usmanhalalit/pixie

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
