<?php

namespace Framework\models;


use Framework\lib\AbstractModel;

class CountriesModel extends AbstractModel
{
    public $country_id;
    public $country_name;
    public $country_code;
    public $status;

    protected static $tableName = 'countries';
    protected static $primaryKey = 'country_id';
    protected static $tableSchema = array(
        'country_name' => self::DATA_TYPE_STR,
        'country_code' => self::DATA_TYPE_STR,
        'status' => self::DATA_TYPE_INT
    );
}