<?php

namespace Framework\lib;


trait FilterInput
{
    private static function CheckValue($value)
    {
        return empty($value) ? null : $value;
    }

    public static function FilterInt($int)
    {
        return is_int(self::CheckValue($int)) ? filter_var(
            $int, FILTER_VALIDATE_INT, FILTER_SANITIZE_NUMBER_INT
        ) : null;
    }

    public static function FilterFloat($float)
    {
        return is_float(self::CheckValue($float)) ? filter_var(
            $float, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION
        ) : null;
    }

    public static function FilterString($string)
    {
        return is_string(self::CheckValue($string)) ? filter_var(
            $string, FILTER_SANITIZE_STRING
        ) : null;
    }

    public static function FilterEmail($email)
    {
        return self::CheckValue($email) ? filter_var(
            $email, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL
        ) : null;
    }

    public static function FilterDateTime($value, $type = 'date_time')
    {
        if (self::CheckValue($value)) {
            if ($type == 'date') {
                if ($date = \DateTime::createFromFormat(DATE_FORMAT, $value)) {
                    return $date->format(DATE_FORMAT);
                } else {
                    return null;
                }
            } elseif ($type == 'time') {
                if ($time = \DateTime::createFromFormat(TIME_FORMAT, $value)) {
                    return $time->format(TIME_FORMAT);
                } else {
                    return null;
                }
            } elseif ($type == 'date_time') {
                if ($date_time = \DateTime::createFromFormat(DATE_TIME_FORMAT, $value)) {
                    return $date_time->format(DATE_TIME_FORMAT);
                } else {
                    return null;
                }
            }
        }
    }

    public static function FilterGender($gender)
    {
        if (self::CheckValue($gender)) {
            return ($gender == 'male' || $gender == 'female'
            || $gender == 'non-binary') ? $gender : null;
        }
    }

    public static function DecodeParam($value)
    {
        return self::CheckValue($value) ? urldecode($value) : null;
    }
}