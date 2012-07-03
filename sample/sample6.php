<?php
require_once dirname(__DIR__).'/lib/PDOx/Autoloader.php';
use PDOx\Autoloader,
    PDOx\Lite;

Autoloader::register();

$pdox = PDOx\Lite::connect(
    array(
        'dsn'       => 'mysql:host=localhost;dbname=pdox',
        'username'  => 'root',
        'password'  => '',
    )
);

$pdox->schema()->table('user')->autopk('id');
$user = $pdox->table('user')->find(2);

var_dump($user);
