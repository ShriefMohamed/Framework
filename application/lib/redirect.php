<?php

namespace Framework\lib;


class Redirect
{
    public static function Home()
    {
        header('location: ' . HOST_NAME);
    }

    public static function NotFound()
    {
        header('location: ' . HOST_NAME . 'notfound');
    }

    public static function To($path, $return = false)
    {
        if ($path !== '') {
            if (Request::Check('get', 'returnURL')) {
                self::ReturnURL();
            } else {
                $returnURL = ($return === true) ? '?returnURL=' . CURRENT_URI : null;
                header('location: ' . HOST_NAME . $path . $returnURL);
            }
        }
    }

    public static function ReturnURL()
    {
        $returnURL = (Request::Check('get', 'returnURL')) ? Request::Get('returnURL', false, true) : null;
        if ($returnURL) {
            header('location: ' . $returnURL);
        } else {
            self::Home();
        }
    }
}