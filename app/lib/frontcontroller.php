<?php

namespace Framework\Lib;

class FrontController
{
    private $_controller = 'index';
    private $_action = 'default';
    private $_params = array();

    const NotFoundController = 'Framework\\Controllers\\NotFoundController';
    const NotFoundAction = 'NotFoundAction';
    public function __construct()
    {
        $this->ParseUrl();
        $this->Dispatch();
    }

    private function ParseUrl()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url = trim($url, '/');
        $url = explode('/', $url);

        if (isset($url[0]) AND $url[0] != '') {
            $this->_controller = $url[0];
        }
        if (isset($url[1]) AND $url[1] != '') {
            $this->_action = $url[1];
        }
        if (isset($url[2]) AND $url[2] != '') {
            $this->_params = explode('/', $url[2]);
        }
    }

    private function Dispatch()
    {
        $controllerClassName = 'Framework\\Controllers\\' . ucfirst($this->_controller) . 'Controller';
        $actionName = ucfirst($this->_action) . 'Action';

        if (!class_exists($controllerClassName)) {
            $controllerClassName = self::NotFoundController;
        }
        $controller = new $controllerClassName;

        if (!method_exists($controller, $actionName)) {
            $this->_action = $actionName = self::NotFoundAction;
        }

        $controller->SetController($this->_controller);
        $controller->SetAction($this->_action);
        $controller->SetParams($this->_params);

        $controller->$actionName();
    }
}

?>