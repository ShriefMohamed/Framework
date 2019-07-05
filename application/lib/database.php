<?php

namespace Framework\lib;

/**
 * Class Database
 *
 * @package Framework\lib
 *
 * Creates a connection with the database.
 *
 * @author Shrief Mohamed
 */
class Database
{
    /**
     * @var connection
     * holds the database connection.
     */
    private static $connection;

    /**
     * Database constructor.
     *
     * private empty construct. exists here to deny creating an object from this class (singleton design pattern).
     */
    private function __construct() {}

    /**
     * Method  * @static  CreateConnection
     *
     * @return \PDO|connection
     *
     * Checks first if there's a connection with the database already, if yes then return it, if not then create one.
     *
     * @author Shrief Mohamed
     */
    public static function CreateConnection()
    {
        if (self::$connection === null)
        {
            try {
                self::$connection = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
            } catch (\Exception $e) {
                echo 'Database connection can not be established. Please try again later.' . '<br>';
                echo 'Error code: ' . $e->getCode();
                exit;
            }
        }

        return self::$connection;
    }
}