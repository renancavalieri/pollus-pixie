<?php

ini_set('display_errors', 1);

require_once __DIR__ . "/../vendor/autoload.php";

use Pollus\Pixie\Manager;

$manager = new Manager();
        
$manager->addConnection
([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'test',
    'username'  => 'root',
    'password'  => 'root',
    'charset'   => 'utf8mb4', // Optional
    'collation' => 'utf8mb4_unicode_ci', // Optional
    'prefix'    => '', // Table prefix, optional
]);

$qb = $manager->getConnection()->getQueryBuilder();
    
if (isset($_GET["name_like"]))
{
    var_dump($qb->table("people")->where("name", "like", $_GET["name_like"])->get());
}
else if (isset($_GET["name_like_wildcard"]))
{
    var_dump($qb->table("people")->where("name", "like", "%" . $_GET["name_like_wildcard"] . "%")->get());
}
else if  (isset($_GET["name_equal"]))
{
    var_dump($qb->table("people")->where("name", "=", $_GET["name_equal"])->get());
}
else if (isset($_GET["id"]))
{
    var_dump($qb->table("people")->where("id", "=", $_GET["id"])->get());
}
else if (isset($_GET["id_find"]))
{
    var_dump($qb->table("people")->find($_GET["id_find"]));
}
else
{
    echo '<pre>python sqlmap.py -u "http://127.0.0.1/path/to/pixie/sqlmap-test/index.php?id=1</pre><br>';
    echo '<pre>python sqlmap.py -u "http://127.0.0.1/path/to/pixie/sqlmap-test/index.php?id_find=1</pre><br>';
    echo '<pre>python sqlmap.py -u "http://127.0.0.1/path/to/pixie/sqlmap-test/index.php?name_like_wildcard=simon</pre><br>';
    echo '<pre>python sqlmap.py -u "http://127.0.0.1/path/to/pixie/sqlmap-test/index.php?name_like=simon</pre><br>';
    echo '<pre>python sqlmap.py -u "http://127.0.0.1/path/to/pixie/sqlmap-test/index.php?name_equal=peter</pre><br>';
}