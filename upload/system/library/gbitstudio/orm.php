<?php
require_once DIR_SYSTEM . 'library/gbitstudio/orm/vendor/autoload.php';
namespace GbitStudio;

class ORM
{
    private static $connection = null;

    public static function init()
    {
        try {
            // Если соединение уже создано, возвращаем QueryBuilder
            if (self::$connection !== null) {
                return self::$connection->getQueryBuilder();
            }

            // Initialize ORM, if needed
            $config = [
                'driver' => 'mysql',
                'host' => DB_HOSTNAME,
                'database' => DB_DATABASE,
                'username' => DB_USERNAME,
                'password' => DB_PASSWORD,
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => DB_PREFIX,
            ];

            // Creates new connection
            self::$connection = new \Pecee\Pixie\Connection('mysql', $config);

            // Get the query-builder object which will initialize the database connection
            return self::$connection->getQueryBuilder();

        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception("ORM initialization failed: " . $e->getMessage());
        }
    }

    // Дополнительные статические методы для удобства
    public static function table($tableName)
    {
        return self::init()->table($tableName);
    }

    public static function getConnection()
    {
        if (self::$connection === null) {
            self::init();
        }
        return self::$connection;
    }
}