<?php


namespace App\CustomFunctions;

use Illuminate\Support\Str;

class CusStFunc
{
    public static function randomStringLower(int $length = 10):string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function randomAlphabetStringLower(int $length = 10):string {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function fixPersianUnicode($persianStr):string{
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
        }, $persianStr);
    }

    public static function arrayKeysToCamel($array):array{
        $result = array();
        foreach ($array as $key=> $val){
            if(is_array($val)){
                $result_t1 =array();
                foreach ($val as $key1=> $val1){
                    if(is_array($val1)){
                        $result_t2 =array();
                        foreach ($val1 as $key2=> $val2){
                            if(is_array($val2)){
                                $result_t3 =array();
                                foreach ($val2 as $key3=> $val3){

                                    $result_t2[Str::camel($key3)] = $val3;
                                }
                                $val2 = $result_t3;
                            }
                            $result_t3[Str::camel($key2)] = $val2;
                        }
                        $val1 = $result_t2;
                    }
                    $result_t1[Str::camel($key1)] = $val1;
                }
                $val = $result_t1;
            }
            $result[Str::camel($key)] = $val;
        }
        return $result;
    }
}
