<?php

use Framework\lib\AbstractModel;
use Framework\lib\Database;
use Framework\Lib\FrontController;
use Framework\lib\Session;

ob_start();

// set displaying error to 1 (1 display) (0 don't display), will be changed in production.
ini_set('display_errors', 1);

// set session settings
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid	', 0);
ini_set('session.save_handler', 'files');

// we defined the DS in index.php but will redefine it just to make sure.
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// define the main domain and the current page for later use whenever we're going to redirect or return.
defined('HOST_NAME') ? null : define('HOST_NAME', 'http://' . $_SERVER['HTTP_HOST'] . '/');
defined('CURRENT_URI') ? null : define('CURRENT_URI', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// define some of the working directories we would need later.
// define application (secure) directories;
defined('APPLICATION_DIR') ? null : define('APPLICATION_DIR', realpath(dirname(__file__)) . DS . '..' . DS);
defined('SESSION_DIR') ? null : define('SESSION_DIR', APPLICATION_DIR . '..' . DS . 'session' . DS);
defined('PUBLIC_DIR') ? null : define('PUBLIC_DIR', APPLICATION_DIR . '..' . DS . 'public' . DS);

defined('CONTROLLERS_DIR') ? null : define('CONTROLLERS_DIR', APPLICATION_DIR . 'controllers' . DS);
defined('MODELS_DIR') ? null : define('MODELS_DIR', APPLICATION_DIR . 'models' . DS);
defined('VIEWS_DIR') ? null : define('VIEWS_DIR', APPLICATION_DIR . 'views' . DS);
defined('TEMPLATES_DIR') ? null : define('TEMPLATES_DIR', VIEWS_DIR . '_templates' . DS);
defined('LIB_DIR') ? null : define('LIB_DIR', APPLICATION_DIR . 'lib' . DS);
defined('VENDOR_DIR') ? null : define('VENDOR_DIR', APPLICATION_DIR . 'vendor' . DS);

// set the logging file
defined('LOG_FILE') ? null : define('LOG_FILE', APPLICATION_DIR . 'logs' . DS . 'log.txt');

// define public (user can see anyway) paths; directories were defined to include from later or something,
// but paths is to link not include, ie. css or javascript files.
defined('PUBLIC_PATH') ? null : define('PUBLIC_PATH', HOST_NAME . 'public/');
defined('CSS_PATH') ? null : define('CSS_PATH', PUBLIC_PATH . 'css/');
defined('JAVASCRIPT_PATH') ? null : define('JAVASCRIPT_PATH', PUBLIC_PATH . 'javascript/');
defined('IMAGES_PATH') ? null : define('IMAGES_PATH', PUBLIC_PATH . 'images/');

// define the date_time format.
defined('DATE_TIME_FORMAT') ? null : define('DATE_TIME_FORMAT', 'Y-m-j H:i:s');
defined('DATE_FORMAT') ? null : define('DATE_FORMAT', 'Y-m-j');
defined('TIME_FORMAT') ? null : define('TIME_FORMAT', 'H:i:s');

// define server timestamp
defined('SERVER_TIMESTAMP') ? null : define('SERVER_TIMESTAMP', $_SERVER['REQUEST_TIME']);
// define server time & date
date_default_timezone_set('UTC');
$date_time = \DateTime::createFromFormat('U', SERVER_TIMESTAMP)
    ->setTimeZone(new DateTimeZone(date_default_timezone_get()));

defined('SERVER_DATE_TIME') ? null : define('SERVER_DATE_TIME', $date_time->format(DATE_TIME_FORMAT));
defined('SERVER_DATE') ? null : define('SERVER_DATE', $date_time->format(DATE_FORMAT));
defined('SERVER_TIME') ? null : define('SERVER_TIME', $date_time->format(TIME_FORMAT));

// define the database credentials to use later at the database class
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'Qiggs');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');

// define encryption key and algorithm for openssl & hash_hmac
define('CIPHER_KEY', '?$@MK"<2B;V\)*#*');
define('CIPHER_ALGORITHM', 'aes-128-cbc');
define('HMAC_ALGORITHM', 'sha256');
define('HMAC_KEY', '?$@MK"<2B;V\)*#*');

// define SMTP configuration
define('SMTP_SERVER', 'smtp1.example.com');
define('SMTP_BACKUP_SERVER', 'smtp2.example.com');
define('SMTP_USERNAME', 'user@example.com');
define('SMTP_PASSWORD', 'password');
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_PORT', 587);

// define the company email for contact & support
define('CONTACT_US_EMAIL', 'support@example.co');


// require autoload so the Classes get called automatically without the need of "require" or "include"
if (file_exists(LIB_DIR . 'AutoLoad.php')) {
    require_once LIB_DIR . 'AutoLoad.php';
}

// start the session for the whole website so we can use $_SESSION anywhere in the website without starting it again
$session = new Session();
$session->Initiate();

// call the method get connection to create a new database connection if one isn't already created,
// then put that connection in the Abstract model in the variable dbConnection, this way every model class
// will have access to the database connection, (only the models interacts with the database so we don't need
// the connection any where else but in the models.
AbstractModel::$dbConnection = Database::CreateConnection();

// call the FrontController class which is the class that will take the url and then require the right classes
// requested by the user in the URL
$FrontController = new FrontController;

ob_end_flush();