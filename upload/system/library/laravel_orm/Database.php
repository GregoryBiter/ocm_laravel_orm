<?php
$phpVersion = PHP_VERSION;
$autoloadFile = '';

if (version_compare($phpVersion, '8.0.0', '>=')) {
    $autoloadFile = DIR_SYSTEM . 'library/eloquent/vendor/autoload.php';
} else {
    $autoloadFile = DIR_SYSTEM . 'library/eloquent/vendor/autoload.php';
}

require_once $autoloadFile;

namespace LaravelOrm;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public static function getConnection()
    {
        static $capsule;

        if (!$capsule) {
        

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
        }

        return $capsule->getDatabaseManager();
    }

    public static function asArray($query)
    {
        return $query->get()->toArray();
    }
}

