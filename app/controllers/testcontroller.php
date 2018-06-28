<?php

namespace Framework\Controllers;
use Framework\Models\TestModel;
use Framework\Lib\AbstractController;

class TestController extends AbstractController
{
    public function DefaultAction()
    {
        $model = new TestModel();
        $model->name = 'ahmed';
        $model->email = 'ahmed@mail.com';
        $model->password = '123456';
        $model->Main();
    }
} 

?>