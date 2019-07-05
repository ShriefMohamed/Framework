<?php

namespace Framework\models;


use Framework\lib\AbstractModel;

class User_secret_questionsModel extends AbstractModel
{
    public $user_id;
    public $question_id;
    public $answer;

    protected static $tableName = 'user_secret_questions';
    protected static $primaryKey = 'secret_question_id';
    protected static $tableSchema = array(
        'user_id' => self::DATA_TYPE_INT,
        'question_id' => self::DATA_TYPE_INT,
        'answer' => self::DATA_TYPE_STR
    );
}