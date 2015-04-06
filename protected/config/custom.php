<?php
/**
 * Created by PhpStorm.
 * User: caimiao
 * Date: 14-8-27
 * Time: 下午12:27
 */

return array(
    'params' => array(
        // this is used in contact page
        'weixin'=>array(
            'APPID'=>'',   //微信官方给的，有这个才能用牛逼功能
            'APPSECRET'=>'',  //微信官方给的，有这个才能用牛逼功能
            'TOKEN'=>'xfl276852', //需程序员自定义的参数，定义时要发到微信，用来验证身份
        ),
        'ticket'=>"abcd1234",
        'wxAdminList' =>  array(
            "ojotVuCe2r8Vv4_kfjfYmCa3n5X0",
            "ojotVuG2WnzcGzdgtuaqF-swZOP0",
            "ojotVuGqfZFQP94-8xnNkrBhYQPM",
            "ojotVuKc7Q845VgM5T2Q2phtI8wc",
            "ojotVuO3HHE6vNYbroWgxOauQGWs",
            "ojotVuPQPNhSLbMBLP3TUWK_vdFs",
            "ojotVuAnqvI6_V8aSYYphXlXOSag",
        ),
        'wxid' => '',
        'sms' => array(
            "username"=>"",
            "password"=>"",
            "url"=>"http://www.go028.cn:888/sdk/service.asmx/sendMessage"
        ),

    )
);