<?php

namespace Framework\lib;

/**
 * Class AbstractModel
 *
 * @package Framework\lib
 *
 * All models extends this class, here exists all the methods needed to interact with the database,
 * Every model class represents a database table, and every model contains:
 *      @static @variable tableName
 *      @static @array tableSchema
 *      @static @variable primaryKey
 * this class uses these properties to perform operations such as create an SQL query like insert pr update,
 * or even delete and also select.
 *
 * @author Shrief Mohamed
 */
class AbstractModel
{
    /**
     * @var dbConnection
     * mainConfig file call the database class and generate a database connection and pass it to this
     * variable in order for the whole abstract model and also the model to be able to interacts with the database
     * and perform their functions.
     */
    public static $dbConnection;

    const DATA_TYPE_INT = \PDO::PARAM_INT;
    const DATA_TYPE_STR = \PDO::PARAM_STR;
    const DATA_TYPE_FLOAT = 4;

    /**
     * Method  BuildSQLString
     *
     * @return SQL String
     *
     * Build Sql String needed by create and update methods,
     * it takes the table schema array from the model and loop on it,
     * then it generates a well formatted sql string.
     *
     * @author Shrief Mohamed
     */
    private function BuildSQLString()
    {
        $string = '';
        foreach (static::$tableSchema as $columnName => $type) {
            if ($this->$columnName !== null && !empty($this->$columnName)) {
                $string .= $columnName . ' = :' . $columnName . ", ";
//                if ($type == 5) {
//                    $string .= $columnName . ' = CAST(:' . $columnName . ' AS DATETIME), ';
//                } elseif ($type == 6) {
//                    $string .= $columnName . ' = CAST(:' . $columnName . ' AS DATE), ';
//                } elseif ($type == 7) {
//                    $string .= $columnName . ' = CAST(:' . $columnName . ' AS TIME), ';
//                } else {
//                    $string .= $columnName . ' = :' . $columnName . ", ";
//                }
            }
        }
        return trim($string, ', ');
    }

    /**
     * Method  PrepareValues
     *
     * @param \PDOStatement $stmt
     *
     * Prepare SQL statement in order to be executed,
     * In other words if SQL is:
     *      (INSERT INTO table SET column = :column).
     * This method will replace ":column" with the value of the column.
     * In reality all this method does is just (Bind Values), but with some flexibility and some conditions.
     *
     * @author Shrief Mohamed
     */
    private function PrepareValues(\PDOStatement &$stmt)
    {
        foreach (static::$tableSchema as $columnName => $type) {
            if ($this->$columnName !== null && !empty($this->$columnName)) {
                if ($type == 4) {
                    $sanitizedValue = filter_var($this->$columnName, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $stmt->bindValue(":{$columnName}", $sanitizedValue);
                } else {
                    $stmt->bindValue(":{$columnName}", $this->$columnName, $type);
                }
            }
        }
    }

    /**
     * Method  Create
     *
     * @return bool
     *
     * Insert values (collected from the model which called this method) into the database,
     * By creating the well formatted SQL query and then prepare it and call the method PrepareValues
     * to bind values, and then execute the sql query and check for errors, and if true, set the last inserted
     * id to the primary key in the table in case needed later.
     *
     * @author Shrief Mohamed
     */
    private function Create()
    {
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . self::BuildSQLString();
        $stmt = self::$dbConnection->prepare($sql);
        $this->PrepareValues($stmt);

        try {
            $stmt->execute();
            $this->{static::$primaryKey} = self::$dbConnection->lastInsertId();
            return true;
        } catch (Exception $e) {
            echo 'Error code: ' . $e->getCode();
            return false;
        }
    }

    /**
     * Method  Update
     *
     * @return bool
     *
     * Update values (collected from the model which called this method) in the database,
     * By creating the well formatted SQL query and then prepare it and call the method PrepareValues
     * to bind values, and then execute the sql query and check for errors.
     *
     * @author Shrief Mohamed
     */
    private function Update()
    {
        $sql = 'UPDATE ' . static::$tableName . ' SET ' . self::BuildSQLString() . ' WHERE ' . static::$primaryKey . ' = ' . $this->{static::$primaryKey};
        $stmt = self::$dbConnection->prepare($sql);
        $this->PrepareValues($stmt);

        try {
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            echo 'Error code: ' . $e->getCode();
            return false;
        }
    }

    /**
     * Method  Save
     *
     * @return bool
     *
     * Decide if we want to insert new record to the database or update an existing record, by check if
     * there's a primary key in the model which called this method, if there's a primary key then this
     * would be an update ,of course, and if there's not, then this is a create/insert.
     *
     * @author Shrief Mohamed
     */
    public function Save()
    {
        return $this->{static::$primaryKey} === null ? $this->Create() : $this->Update();
    }

    /**
     * Method  Delete
     *
     * @return bool
     *
     * Delete a specific record from the database by generating the sql query and use the primary key in the model
     * which called this method. and after that execute the sql query.
     *
     * @author Shrief Mohamed
     */
    public function Delete()
    {
        $sql = 'DELETE FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . ' = ' . $this->{static::$primaryKey};
        $stmt = self::$dbConnection->prepare($sql);

        try {
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            echo 'Error code: ' . $e->getCode();
            return false;
        }
    }

    /**
     * Method  * @static  GetAll
     *
     * @param string $options
     *
     * @param bool $paginate
     * @return array|bool
     *
     * Select all the records from a specific table in the database,
     * generate a SELECT query and execute it and then if true, fetch all as an array and within the array,
     * all the records as objects from the model class which called this method.
     * PS. after a true execution, and after a fetchAll been performed it checks if the results is an array,
     * and, of course, if the results was not empty; if conditions was a false, then return false.
     *
     * @author Shrief Mohamed
     */
    public static function GetAll($options = '', $paginate = false)
    {
        $sql = "SELECT * FROM " . static::$tableName . ' ' . $options;

        if ($paginate) {
            $per_page = $paginate[1];
            $total_records = self::Count();
            $start_from = ($paginate[0]-1) * $per_page;
            $total_pages = ceil($total_records / $per_page);

            $sql .= " LIMIT " . $start_from . ', ' . $per_page;
        }

        $stmt = self::$dbConnection->prepare($sql);
        if ($stmt->execute() === true) {
            $results = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, get_called_class());

            if (is_array($results) && !empty($results)) {
                if ((isset($total_records) && $total_records !== null) && (isset($total_pages) && $total_pages !== null)) {
                    $results = array('results' => $results, 'total_records' => $total_records, 'total_pages' => $total_pages);
                }

                return $results;
            }
        } else {
            return false;
        }
    }

    /**
     * Method  * @static  GetOne
     *
     * @param $pk
     *
     * @return bool
     *
     * Select a specific record from the database, using the primary key in the model which called this method.
     * then execute the query and fetch the result as an object, and check if it's object and its not empty
     * before returning it. if not then return false.
     *
     * @author Shrief Mohamed
     */
    public static function GetOne($pk)
    {
        $sql = "SELECT * FROM " . static::$tableName . ' WHERE ' . static::$primaryKey . ' = ' .  $pk;
        $stmt = self::$dbConnection->prepare($sql);
        if ($stmt->execute() === true) {
            $result = $stmt->fetchObject(__CLASS__);
            return (is_object($result) && !empty($result)) ? $result : false;
        } else {
            return false;
        }
    }

    /**
     * Method  * @static  GetSQL
     *
     * @param $sql
     * @param array $options
     *
     * @return array|bool
     *
     * Takes the SQL query as an parameter and also the options and loop on the options to get the type from it and
     * also the value. then bind value to replace :value with $value.
     * then if execution is true fetch all as array of objects and check if results is array and not empty. if yes
     * the return the results. else return false.
     *
     * @author Shrief Mohamed
     */
    public static function GetSQL($sql, $options = array(), $fetch = 1)
    {
        $stmt = self::$dbConnection->prepare($sql);
        if (!empty($options)) {
            foreach ($options as $columnName => $option) {
                $type = $option[0];
                $value = $option[1];

                if ($type == 4) {
                    $sanitizedValue = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $stmt->bindValue(":{$columnName}", $sanitizedValue);
                } else {
                    $stmt->bindValue(":{$columnName}", $value, $type);
                }
            }
        }

        if ($stmt->execute() === true) {
            if ($fetch !== 1) {
                $result = $stmt->fetchObject(get_called_class());
                return (is_object($result) && !empty($result)) ? $result : false;
            } else {
                $results = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, get_called_class());
                return (is_array($results) && !empty($results)) ? $results : false;
            }
        } else {
            return false;
        }
    }

    /**
     * Method  * @static  ExecuteSQL
     *
     * @param string $sql
     * @param string $returnType
     *
     * @return array|bool
     *
     * Take SQL query and a return type (object/array), and execute it.
     * after executing it if the return type is object then fetch object and check if the result is object and not
     * empty else then fetch all as an array of objects and check also if the result is array and not empty and return.
     * if in both cases theres any issue (empty/not object/not array) then return false.
     *
     * @author Shrief Mohamed
     */
    public static function ExecuteSQL($sql = '', $returnType = 'array') //returnType = array/object
    {
        if ($sql != null) {
            $stmt = self::$dbConnection->prepare($sql);
            if ($stmt->execute() === true) {
                if ($returnType !== 'array') {
                    $result = $stmt->fetchObject(__CLASS__);
                    return (is_object($result) && !empty($result)) ? $result : false;
                } else {
                    $results = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, get_called_class());
                    return (is_array($results) && !empty($results)) ? $results : false;
                }
            } else {
                return false;
            }
        }
    }

    public static function Count($options = '')
    {
        $sql = "SELECT COUNT(*) FROM " . static::$tableName . ' ' . $options;
        $stmt = self::$dbConnection->prepare($sql);
        if ($stmt->execute() === true) {
            $count = $stmt->fetchColumn();
            return ($count == false) ? 0 : $count;
        }
    }

    public static function GetTitles($language, $key)
    {
        $sql = "SELECT " . $language . " FROM " . static::$tableName . " WHERE title_key = '" . $key . "'";
        return self::ExecuteSQL($sql, 'object');
    }
}