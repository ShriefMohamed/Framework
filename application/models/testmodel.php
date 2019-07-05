<?php

namespace Framework\models;


use Framework\lib\AbstractModel;

class TestModel extends AbstractModel
{
    public $id;
    public $string;
    public $number;
    public $date;
    public $time;
    public $date_time;

    protected static $tableName = 'test';
    protected static $tableSchema = array(
        'string' => self::DATA_TYPE_STR,
        'number' => self::DATA_TYPE_INT,
        'date' => self::DATA_TYPE_STR,
        'time' => self::DATA_TYPE_STR,
        'date_time' => self::DATA_TYPE_STR
    );
    protected static $primaryKey = 'id';

    public static function GetByString($value)
    {
        $sql = "SELECT * FROM " . static::$tableName . ' WHERE string = :string';
        $result = parent::GetSQL($sql, array('string' => array(parent::DATA_TYPE_STR, $value)));
        return $result;
    }
}