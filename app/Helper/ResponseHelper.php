<?php

namespace App\Helper;


class ResponseHelper
{

    public static function convertValidation($error){
        $response = [];
        foreach($error as $key => $value){
            $response[$key] =  $value[0];
        }
        return $response;
    }

}
