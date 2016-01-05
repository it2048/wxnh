<?php

/**
 * Created by PhpStorm.
 * User: xiongfanglei
 * Date: 14-11-25
 * Time: 下午9:33
 */
class CheckInfo {

    /**
     * 验证Email格式
     * @param type $email
     */
    public static function email($email) {
        if (preg_match('/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,4}$/',$email)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * 正则验证手机号码
     * @param type $phone
     * @return boolean
     */
    public static function phone($phone)
    {
        $reg = '/^1[34578][0-9]{9}/';
        if (preg_match($reg,$phone)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * 验证QQ号码
     * @param type $qq
     * @return boolean
     */
    public static function qq($qq)
    {
        if (preg_match('/^\d[0-9]{5,15}$/',$qq)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function check_account($str)
    {
        $reg3 = '/^[\_a-zA-Z0-9]+$/';
        if (preg_match($reg3,$str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function filter_utf8_char($ostr){
        preg_match_all('/[\x{FF00}-\x{FFEF}|\x{0000}-\x{00ff}|\x{4e00}-\x{9fff}]+/u', $ostr, $matches);
        $str = join('', $matches[0]);
        if($str==''){
            $returnstr = '';
            $i = 0;
            $str_length = strlen($ostr);
            while ($i<=$str_length){
                $temp_str = substr($ostr, $i, 1);
                $ascnum = Ord($temp_str);
                if ($ascnum>=224){
                    $returnstr = $returnstr.substr($ostr, $i, 3);
                    $i = $i + 3;
                }elseif ($ascnum>=192){
                    $returnstr = $returnstr.substr($ostr, $i, 2);
                    $i = $i + 2;
                }elseif ($ascnum>=65 && $ascnum<=90){
                    $returnstr = $returnstr.substr($ostr, $i, 1);
                    $i = $i + 1;
                }elseif ($ascnum>=128 && $ascnum<=191){ // 特殊字符
                    $i = $i + 1;
                }else{
                    $returnstr = $returnstr.substr($ostr, $i, 1);
                    $i = $i + 1;
                }
            }
            $str = $returnstr;
            preg_match_all('/[\x{FF00}-\x{FFEF}|\x{0000}-\x{00ff}|\x{4e00}-\x{9fff}]+/u', $str, $matches);
            $str = join('', $matches[0]);
        }
        return $str;
    }

}
