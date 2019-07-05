<?php

namespace Framework\models;


use Framework\lib\AbstractModel;

class FeedbackModel extends AbstractModel
{
    public $feedback_id;
    public $title_key;
    public $en;
    public $de;
    public $fr;
    public $it;
    public $ro;

    protected static $tableName = 'feedback';
    protected static $primaryKey = 'feedback_id';
    protected static $tableSchema = array(
        'title_key' => self::DATA_TYPE_STR,
        'en'  => self::DATA_TYPE_STR,
        'de' => self::DATA_TYPE_STR,
        'fr' => self::DATA_TYPE_STR,
        'it' => self::DATA_TYPE_STR,
        'ro' => self::DATA_TYPE_STR
    );
}