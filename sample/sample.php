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

$res = $pdox->table('user')->all();

foreach ($res as $row) {
    var_dump($row);
}
