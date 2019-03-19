<?php

try {
    //$mysql = new PDO('mysql:host=127.0.0.1;port=3306;dbname=wx;', 'root', 'XFkj!@#$8888');
    $mysql = new PDO('mysql:host=127.0.0.1;port=3306;dbname=admin_v3;', 'root', 'root');
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    file_put_contents(__DIR__ . '/mysql_error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
    die('error');
}