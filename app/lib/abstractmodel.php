<?php

namespace Framework\Lib;

class AbstractModel
{
    public static $db; 

    ##### BuildSQLstring ##########
    // Parameters :- None
    // Return :- SQL Query
    //Purpose :- every database table has a model only for it
    // so because of that at every model we create an array contains the table's columns
    // and then we loop that array and arrange these coulmns and put the data on it and by that
    // this function can really generate the sql strings like insert and update
    ###########################
    public static function BuildSqlString()
    {
        $params = '';
        foreach(static::$tableSchema as $columnName) {
            $params .= $columnName . ' = :' . $columnName . ', ';
        }
        return trim($params, ', ');
    }

    private function BindValues($stmt)
    {
        foreach (static::$tableSchema as $columnName) {
            $stmt->bindValue(":{$columnName}", $this->$columnName);
        }
    }

    public function Create()
    {
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . self::BuildSqlString();
        $stmt = $Connection->prepare($sql);
        $this->BindValues($stmt);                
        $stmt->execute();
        
    }
}

?>