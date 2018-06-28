<?php

namespace Framework\Lib;

class AbstractController
{
    protected $_controller;
    protected $_action;
    protected $_params;

    public function NotFoundAction()
    {
        $this->SetView();
    }

    public function SetController($controller)
    {
        $this->_controller = $controller; 
    }

    public function SetAction($action)
    {
        $this->_action = $action;
    }

    public function SetParams($params)
    {
        $this->_params = $params;
    }

    public function SetView()
    {
        if ($this->_action == FrontController::NotFoundAction) {
            require_once(VIEWS_PATH . 'notfound' . DS . 'notfound.view.php');
        } else {
            require_once(VIEWS_PATH . $this->_controller . DS . $this->_action . '.view.php');
        }
    }
}

?>