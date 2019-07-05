<?php

namespace Framework\lib;


trait Helper
{
    public static function HashPassword($password)
    {
        $cipher = new Cipher;
        return $cipher->Hash($password);
    }

    public static function DateDiff($date, $type = 'date_time')
    {
        if ($date) {
            if ($type == 'date') {
                return \DateTime::createFromFormat(DATE_FORMAT, $date)
                    ->diff(new \DateTime());
            } elseif ($type == 'time') {
                return \DateTime::createFromFormat(TIME_FORMAT, $date)
                    ->diff(new \DateTime());
            } elseif ($type == 'date_time') {
                return \DateTime::createFromFormat(DATE_TIME_FORMAT, $date)
                    ->diff(new \DateTime());
            }
        }
    }

    public static function TimeElapsed($datetime, $full = false) {
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function ReArrayFiles(&$file_post)
    {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }
}