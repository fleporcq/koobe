<?php namespace App\Helpers;

class String {

    public static function sanitize($string, $capitalize = false)
    {
        $string = trim(strip_tags($string));
        if ($capitalize) {
            $string = ucfirst(strtolower($string));
        }
        return $string;
    }

}