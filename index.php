<?php

// define directory separator in order to use it to navigate to configuration folder and init the main config file.
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// define configurations directory to get the main config from it.
define('CONFIG_DIR', 'application' . DS . 'config' . DS);

// if the file main config exists then require it.
if (file_exists(CONFIG_DIR . 'mainconfig.php'))
{
    require_once CONFIG_DIR . 'mainconfig.php';
}

?>