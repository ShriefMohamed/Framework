<?php

namespace Framework\models;


use Framework\lib\AbstractModel;

class User_roleModel extends AbstractModel
{
    public $role_id;
    public $role;

    protected static $tableName = 'user_role';
    protected static $primaryKey = 'role_id';
    protected static $tableSchema = array(
        'role' => self::DATA_TYPE_STR
    );
}