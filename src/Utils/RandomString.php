<?php

namespace App\Utils;

class RandomString
{
    public function random($nb = 30)
    {
        // for slugUrl build
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for($i=0; $i<$nb; $i++){
            $string .= $chars[rand(0, strlen($chars)-1)];
        }

        return $string;
    }

}