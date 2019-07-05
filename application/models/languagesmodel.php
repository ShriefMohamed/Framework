<?php

namespace Framework\models;


use Framework\lib\AbstractModel;

class LanguagesModel extends AbstractModel
{
    public $language_id;
    public $language_name;
    public $language_code;
    public $status;

    protected static $tableName = 'languages';
    protected static $primaryKey = 'language_id';
    protected static $tableSchema = array(
        'language_name' => self::DATA_TYPE_STR,
        'language_code' => self::DATA_TYPE_STR,
        'status' => self::DATA_TYPE_INT
    );
}