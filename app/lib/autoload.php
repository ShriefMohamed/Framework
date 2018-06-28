<?php

namespace Framework\Lib;

class AutoLoad 
{
    ##### autoload ##########
    // Parameters :- a:ClassName
    // Return Type :- None
    // Purpose :- here we take any called class and remove from it the "namespace" we working under
    // and then remove any \ from it and then make it lower case and add to the end of it ".php"
    // and by that we have instead of the class we call a name of php file that can be required easily
    // so we just do a require_once to this file (class)    
    ###########################
    public static function autoload($classname)
    {
        $classname = str_replace('Framework', '', $classname);
        $classname = strtolower($classname);
        $classname = str_replace('\\', DS, $classname);
        $classname = $classname . '.php';

        if (file_exists(APP_PATH . $classname)) {
            require_once(APP_PATH . $classname);
        }
    }
}

spl_autoload_register(__NAMESPACE__ . '\AutoLoad::autoload');

?>