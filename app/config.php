<?php

use Framework\Lib\FrontController;
use Framework\Lib\AbstractModel;
use Framework\Lib\Database;

ob_start();

//define DIRECTORY_SEPARATOR
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

//config error displaying
ini_set('display_errors', 1);

//define the paths
define('APP_PATH', realpath(dirname(__FILE__)) . DS);
define('VIEWS_PATH', APP_PATH . 'views' . DS);

//define database configs
define('DB_HOST', 'localhost');
define('DB_NAME', 'framework');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// require autoload so the Classes get called automaticly without the need of "require" or "include"
if (file_exists(APP_PATH . DS . 'lib' . DS . 'autoload.php')) {
	require_once APP_PATH . DS . 'lib' . DS . 'autoload.php';
}

// start the session for the whole website so we can use $_SESSION anywhere in the website without starting it again
session_start();

// start the database connection and put it at the variable "$db"
$db = Database::Connect();
// put the database connection in the AbstractModel class
// the abstract model is the main model that all the other model classes will extend,
// and the models are the only classes that intracts with the database so by adding the database connection at
// the abstract model then every model class will have access to the database connection
AbstractModel::$db = $db;
// call the FrontController class which is the class that will take the url and then require the right files and classes
$FrontController = new FrontController;

ob_flush();

?>