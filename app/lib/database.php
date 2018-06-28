<?php

namespace Framework\Lib;

class Database
{
    private static $Connection;

    private function __construct() {}

    ##### Connect ##########
    // Parameters :- None
    // Return Type :- Database Connection
    // Purpose :- Connect to the database and return that connection.
    ###########################
    public function Connect()
    {
        if (self::$Connection === null) {
            try {
                $Connection = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
            } catch (PDOException $e) {
				echo 'Database connection can not be estabilished. Please try again later.' . '<br>';
                echo 'Error Code : ' . $e->getCode();
                exit;
            }
        }
        return $Connection;
    }
}


?>