<?php

namespace App\Library;
use Illuminate\Support\Str;

class Helper
{

    public static function convertToEnNumber($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠'];

        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
        return $englishNumbersOnly;
    }

    public static function convertToEnNumberInputs($inputs = [], $keys = [])
    {
        $_i = [];
        foreach ($keys as $key){
            if(array_key_exists($key,$inputs))
                $_i[$key]  = Helper::convertToEnNumber($inputs[$key]);
    }
        return array_merge($inputs,$_i);

    }

    public static function sanitizeNumberInput($string)
    {
        // 1. تبدیل اعداد فارسی و عربی به انگلیسی
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
        $string = str_replace($persian, $english, $string);
        $string = str_replace($arabic, $english, $string);
        // 2. حذف هر چیزی جز اعداد (شامل جداکننده‌های هزارگان: «٬» فارسی، «,» انگلیسی، فاصله و غیره)
        $cleanNumber = preg_replace('/[^\d]/', '', $string);
    
        return $cleanNumber;
    }

}
