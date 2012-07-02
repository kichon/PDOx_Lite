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

$users = $pdox->table('user')->search(array('name' => 'kikuchi'));

while ($user = $users->next()) {
    echo "id: $user->id, name: $user->name", PHP_EOL;
}
