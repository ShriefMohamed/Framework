<?php


namespace Framework\lib;


class Session extends \SessionHandler
{
    private $sessionName = 'Framework';
    private $sessionSavePath = SESSION_DIR;
    private $sessionRenewTime = 1;

    public function __construct()
    {
        session_name($this->sessionName);
        session_save_path($this->sessionSavePath);
        session_set_save_handler($this, true);
    }

    public function Initiate()
    {
        if('' === session_id()) {
            if (session_start()) {
                $this->Check();
            }
        }
    }

    private function Check()
    {
        if (self::Exists('session_start_time')) {
            if (SERVER_TIMESTAMP - self::Get('session_start_time') > ($this->sessionRenewTime * 60)) {
                $this->Renew();
            }
        } else {
            $this->SetStartTime();
        }
    }

    private function Renew()
    {
        $this->SetStartTime();
        return session_regenerate_id(true);
    }

    private function SetStartTime()
    {
        if (!self::Exists('session_start_time')) {
            self::Set('session_start_time', SERVER_TIMESTAMP);
        }
    }

    public static function Exists($key)
    {
        return (isset($_SESSION[$key])) ? true : false;
    }

    public static function CookieExists($key)
    {
        return(isset($_COOKIE[$key])) ? true : false;
    }

    public static function Set($key, $value)
    {
        if ('' !== $key && '' !== $value) {
            $cipher = new Cipher;
            $_SESSION[$key] = $cipher->Encrypt($value);
        }
    }

    public static function Get($key)
    {
        $cipher = new Cipher;
        return ((false !== self::Exists($key)) && !empty($_SESSION[$key])) ? $cipher->Decrypt($_SESSION[$key]) : false;
    }

    public static function Remove($key)
    {
        if ($key !== '') {
            if (self::Exists($key)) {
                unset($_SESSION[$key]);
            }
        }
    }

    public static function SetCookie($key, $value, $expire = 1)
    {
        if ('' !== $key && '' !== $value) {
            setcookie($key, json_encode($value), SERVER_TIMESTAMP + $expire*3600, '/');
        }
    }

    public static function GetCookie($key, $decode = false)
    {
        if ((self::CookieExists($key) && !empty($_COOKIE[$key]))) {
            if ($decode !== false) {
                return $_COOKIE[$key];
            } else {
                return json_decode($_COOKIE[$key]);
            }
        } else {
            return false;
        }
    }

    public static function RemoveCookie($key)
    {
        if ($key !== '') {
            if (self::CookieExists($key)) {
                setcookie($key, '', SERVER_TIMESTAMP - 3600);
            }
        }
    }

    public static function KillAll()
    {
        session_unset();
        session_destroy();
    }
}