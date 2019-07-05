<?php


namespace Framework\Lib;


/**
 * Class FrontController
 *
 * @package Framework\Lib
 *
 * Controls when the user types a URL, take the url and split it and call the required classes and methods/actions.
 *
 * @author Shrief Mohamed
 */
class FrontController
{
    // declare default controller and action to be used later in dispatch in case if only the user
    // typed the domain name with no additional parameters.

    /**
     * @var @string _controller
     * default controller
     */
    private $_controller = 'index';

    /**
     * @var @string _action
     * default action
     */
    private $_action = 'default';

    /**
     * @var array _params
     * parameters from the url
     */
    private $_params = array();

    // declare not found controller and not found action in case if the user typed strange parameters after the domain name.
    const NOT_FOUND_CONTROLLER = 'Framework\Controllers\NotFoundController';
    const NOT_FOUND_ACTION = 'NotFoundAction';

    /**
     * FrontController constructor.
     *
     * whenever this class is called (which is every time the site loads, because every time the site go to index,
     * then index go to main config, then main config call this class), it automatically calls parseUrl and dispatch.
     */
    public function __construct()
    {
        $this->ParseUrl();
        $this->Dispatch();
    }

    /**
     * Method  ParseUrl
     *
     * get the url and cut it to pieces and use it in the dispatch function,
     * the first pieces of the url we will call it the controller which is a php class,
     * the second one will be the action or the function at the controller (class),
     * the last part will be the parameters and it will be an array,
     * so when the url for example is: www.x.com/index/post/5 this means: the index class (controller),
     * and the post function (action) and the parameter is 5 which in this case the id of the post we want to display,
     * and notice that if the url is empty, like: www.x.com then we don't have any thing to cut and call,
     * so in this case we already have a default value for the class and action that should be used in this case,
     * these default values are (index controller and default action).
     *
     * @author Shrief Mohamed
     */
    private function ParseUrl()
    {
        $url = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'), 3);

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

    /**
     * Method  Dispatch
     *
     * Now after spiting the url to small pieces we know what they mean (because later on we will put them there,
     * or redirect the user there; we need to use these pieces and call some classes and functions with it so that we
     * get the most automated system can be.
     *
     * @author Shrief Mohamed
     */
    private function Dispatch()
    {
        /*
         * for the security of the admin portal we will check every time here before any class get called if
         * this user is logged in or not, if not logged in then we will take him to the login,
         * else let him continue to the admin dashboard.
         * and same goes for the users. by this we ensure that no one will be able to access
         * any portal unless he is logged in with the right credentials.
         * also if the user tries to go to the login page and he's already logged in then we will
         * take him to the homepage, no logged in user should be able to go to the login page.
         * */
//        if ($this->_controller == 'admin') {
//            if (Session::Exists('logged_in') !== true || Session::Get('logged_in')->role !== 'admin') {
//                $this->_controller = 'login';
//                $this->_action = 'default';
////                Redirect::To('login/login', true);
//            }
//        } elseif ($this->_controller == 'user') {
//            if (Session::Exists('logged_in') !== true || Session::Get('logged_in')->role !== 'user') {
//                $this->_controller = 'login';
//                $this->_action = 'default';
////                Redirect::To('login/default', true);
//            }
//        } elseif ($this->_controller == 'login') {
//            if (Session::Exists('logged_in') == true) {
//                Redirect::Home();
//            }
//        }

        /*
        * first we make the controller name we got from the url full, which means add to it the namespace first,
        * then make the first litter of the class capital case (which is what we will do in all our classes),
        * then at the end add the work 'Controller'.
        * so now for example the work index we got from the URL will become: Qiggs\Controllers\IndexController
        * in this case, the autoload class will be able to know which class is this.
        * */
        $controllerClassName = 'Framework\Controllers\\' . ucfirst($this->_controller) . 'Controller';

        /*
        * for the frontcontroller to be able to call a function at the controller automatically just by splitting the url,
        * we have to add Action after the function/action name, it's like a secret code between us so it can be able to
        * identify an action method and call it.
        * the DefaultAction get called if the url is: www.x.com/index/default ->
        *      index: the controller class name,
        *      default: the action/function's name
        */
        $actionName = ucfirst($this->_action) . 'Action';

        // check first if this controller class exists, if not then take him to the not found class
        if (!class_exists($controllerClassName)) {
            $controllerClassName = self::NOT_FOUND_CONTROLLER;
        }

        // if everything is ok so far then initiate the class
        $controller = new $controllerClassName();

        // check if the function (action) exists in that initiated class, if not then display 404 not found (not found action).
        if (!method_exists($controller, $actionName)) {
            $this->_action = $actionName = self::NOT_FOUND_ACTION;
        }

        // inside of the class that got initiated (in their parent class to be specific, inside Abstract Controller)
        // set these variables (the name of the controller and action and parameters)
        // in case of we needed to use them there and of course inside the controller class we don't have access
        // to this class so this is the perfect way to parse these values there.
        $controller->SetContActParam($this->_controller, $this->_action, $this->_params);

        // finally call the function (action) at the class (controller) that got initiated.
        $controller->$actionName();

        // now we called a class and also called an action/function in that class so this file will now go to the
        // called class and action.
    }
}