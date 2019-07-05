<?php

namespace Framework\lib;


/**
 * Class Request
 *
 * @package Framework\lib
 *
 * Handles POST/GET requests
 *
 * @author Shrief Mohamed
 */
class Request
{
    /**
     * @var array
     *
     * the returned POST/GET values
     */
    public static $data;

    /**
     * @var string
     *
     * the strength of sanitization. default: normal
     */
    public static $strength = 'normal';

    /**
     * Method  * @static  Check
     *
     * @param string $type
     * @param $name
     *
     * @return bool
     *
     * Checks if there's a POST/GET request.
     *
     * @author Shrief Mohamed
     */
    public static function Check($type = 'post', $name)
    {
        return $type == 'get' ? ((isset($_GET[$name]) && !empty($_GET[$name])) ? true : false) : ((isset($_POST[$name]) && !empty($_POST[$name])) ? true : false);
    }

    /**
     * Method * @static  Get
     *
     * @param string $name the name of the value sought
     * @param bool $urlDecode set to TRUE if the method should urldecode the value
     * @param bool $sanitize set to TRUE if the method should sanitize the value against XSS vulnerabilities
     *
     * @return mixed
     *
     * Method to set, clean &/or sanitize a $_GET value if set. after it's done,
     * it will set the request value into the $data property or it will set it null.
     * then finally it will return the $param.
     *
     * @author Shrief Mohamed
     */
    public static function Get($name = '', $urlDecode = false, $sanitize = false)
    {
        if (self::Check('get', $name)) {
            if (true === $urlDecode && true === $sanitize) {
                self::$data[$name] = self::Clean(self::Sanitize($_GET[$name]), true);
            } elseif (true === $urlDecode && false === $sanitize) {
                self::$data[$name] = self::Clean($_GET[$name], true);
            } elseif (true === $sanitize && false === $urlDecode) {
                self::$data[$name] = self::Clean(self::Sanitize($_GET[$name]),false);
            } else {
                self::$data[$name] = self::Clean($_GET[$name], false);
            }
        } else {
            self::$data[$name] = null;
        }
        return self::$data[$name];
    }

    /**
     * Method * @static  Post
     *
     * @param string $name the name of the value sought
     * @param bool $urlDecode set to TRUE if the method should urldecode the value
     * @param bool $sanitize set to TRUE if the method should sanitize the value against XSS vulnerabilities
     *
     * @return mixed
     *
     * Method to set, clean &/or sanitize a $_POST value if set. after it's done,
     * it will set the request value into the $data property or it will set it null.
     * then finally it will return the $param.
     *
     * @author Shrief Mohamed
     */
    public static function Post($name='', $urlDecode = false, $sanitize = false)
    {
        if (self::Check('post', $name)) {
            if (true === $urlDecode && true === $sanitize) {
                self::$data[$name] = self::Clean(self::Sanitize($_POST[$name]), true);
            } elseif (true === $urlDecode && false === $sanitize) {
                self::$data[$name] = self::Clean($_POST[$name], true);
            } elseif (true === $sanitize && false === $urlDecode) {
                self::$data[$name] = self::Clean(self::Sanitize($_POST[$name]), false);
            } else {
                self::$data[$name] = self::Clean($_POST[$name], false);
            }
        } else {
            self::$data[$name] = null;
        }
        return self::$data[$name];
    }

    /**
     * Method  Clean
     *
     * @param $data
     * @param bool $isUrlEncoded
     *
     * @return string
     *
     * Private method to clean data
     *
     * @author Shrief Mohamed
     */
    private static function Clean($data, $isUrlEncoded = false) {
        return ($isUrlEncoded) ? strip_tags(trim(urldecode($data))) : strip_tags(trim($data));
    }

    /**
     * Method  Sanitize
     *
     * @param $data
     *
     * @return string
     *
     * Private method to sanitize data
     *
     * @author Shrief Mohamed
     */
    private static function Sanitize($data) {
        switch(static::$strength){
        default:
            return htmlspecialchars($data, ENT_QUOTES, "UTF-8");
            break;
        case 'strong':
            return htmlentities($data, ENT_QUOTES | ENT_IGNORE, "UTF-8");
            break;
        case 'strict':
            return urlencode($data);
            break;
        }
    }
}