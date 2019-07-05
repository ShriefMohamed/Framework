<?php

namespace Framework\Lib;

/**
 * Class AutoLoad
 *
 * @package Framework\Lib
 *
 * Call the classes automatically.
 *
 * @author Shrief Mohamed
 */
class AutoLoad
{
    /**
     * Method  * @static  autoload
     *
     * @param $classname
     *
     * Take the class name and remove from it the namespace and replace the "\" with DS.
     * then lower the classname because all the classes we will create the file will be in lower case.
     * then after that require the classname.
     *
     * @author Shrief Mohamed
     */
    public static function autoload($classname)
    {
        $classname = preg_replace('/Framework/', '', $classname);
        $classname = preg_replace("/\\\\/", DS, $classname);
        $classname = strtolower($classname);
        $classname = $classname . '.php';

        if (file_exists(APPLICATION_DIR . $classname)) {
            require_once APPLICATION_DIR . $classname;
        }
    }
}

spl_autoload_register(__NAMESPACE__ . '\AutoLoad::autoload');

?>