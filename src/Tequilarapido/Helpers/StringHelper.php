<?php namespace Tequilarapido\Helpers;

class StringHelper
{
    public static function replacePlaceHolders($string, $data)
    {
        foreach ($data as $key => $value) {
            $string = str_replace('@' . $key . '@', $value, $string);
        }

        return $string;
    }


    public static function sanitizeSerialized($string)
    {
        $string = str_replace('\"', '"', $string);
        $string = str_replace('"', '\"', $string);

        return $string;
    }

}