<?php

namespace Framework\Controllers;
use Framework\Lib\AbstractController;

class IndexController extends AbstractController
{
    public function DefaultAction()
    {
        $this->SetView();
    }
}

?>