<?php
require_once dirname(__DIR__).'/lib/PDOx/Lite.php';

$pdox = PDOx\Lite::connect(array('mysql:host=localhost;dbname=pdox', 'root', ''));
