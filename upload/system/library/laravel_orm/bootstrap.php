<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$phpVersion = PHP_VERSION;
$vendorDir = '';

if (version_compare($phpVersion, '8.0.0', '>=')) {
    $vendorDir = DIR_SYSTEM . 'library/laravel_orm/php8.2/vendor';
    $autoloadFile = DIR_SYSTEM . 'library/laravel_orm/php8.2/vendor/autoload.php';
} else {
    $vendorDir = DIR_SYSTEM . 'library/laravel_orm/php7.4/vendor';
    $autoloadFile = DIR_SYSTEM . 'library/laravel_orm/php7.4/vendor/autoload.php';
}

require_once $autoloadFile;
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => DB_HOSTNAME,
    'database'  => DB_DATABASE,
    'username'  => DB_USERNAME,
    'password'  => DB_PASSWORD,
    'charset'   => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix'    => DB_PREFIX,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();