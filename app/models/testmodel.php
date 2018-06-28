<?php

namespace Framework\Models;
use Framework\Lib\AbstractModel;

class TestModel extends AbstractModel
{
    public $id;
    public $name;
    public $email;
    public $password;

    public static $tableName = 'test';
    public static $tableSchema = array(
        'name',
        'email',
        'password'
    );

    public function Main()
    {
        $abstract = new self;
        $abstract::create();
    }
}

?>