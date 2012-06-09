<?php
require_once dirname(__DIR__).'/lib/PDOx/Autoloader.php';
use PDOx\Autoloader,
    PDOx\Lite;

Autoloader::register();

$db = new Lite();

$db->connect(
    'master',
    array(
        'host'      => 'localhost',
        'dbname'    => 'pdox',
        'user'      => 'root',
        'password'  => ''
    )
);

$db->exec("
    create table user(
        id int not null auto_increment, 
        name varchar(255) not null,
        created_at timestamp default now(),
        key (id)
    )engine=innodb, default charset=utf8
");

$db->insert('user', array(
    'name' => 'kichon'
));

$sth = $db->select('user',
    array(
        'id', 'name', 'created_at'
    ),
    array(
        'id' => 1
    )
);

$db->update('user',
    array(
        'name' => 'kichon2'
    ),
    array(
        'name' => 'kichon'
    )
);

$db->delete('user', array(
    'name' => 'kichon2'
));
