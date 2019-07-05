<?php

namespace Framework\models;


use Framework\lib\AbstractModel;

class User_addressModel extends AbstractModel
{
    public $address_id;
    public $user_id;
    public $country_id;
    public $state;
    public $street;
    public $zip;

    protected static $tableName = 'user_address';
    protected static $primaryKey = 'address_id';
    protected static $tableSchema = array(
        'user_id' => self::DATA_TYPE_INT,
        'country_id' => self::DATA_TYPE_INT,
        'state' => self::DATA_TYPE_STR,
        'street' => self::DATA_TYPE_STR,
        'zip' => self::DATA_TYPE_INT
    );
}