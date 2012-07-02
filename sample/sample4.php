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

$users = $pdox->table('user')->search(array('name' => 'kichon'))->get_column(array('name'));


foreach ($users as $user) {
    var_dump($user['name']);
}
