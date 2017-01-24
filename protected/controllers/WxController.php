<?php

/**
 * SiteController is the default controller to handle user requests.
 */
class WxController extends CController
{
    public function actionIndex()
    {
        $weixin = new Wxmessage(Yii::app()->params['weixin']);
        //如果不为post则需要验证权限
        if (!Yii::app()->request->isPostRequest) {
            $echostr = Yii::app()->request->getParam('echostr');
            $signature = Yii::app()->request->getParam('signature');
            $timestamp = Yii::app()->request->getParam('timestamp');
            $nonce = Yii::app()->request->getParam('nonce');
            $weixin->checkSignature($signature, $timestamp, $nonce);
        }
    }

    public function actionGetToken()
    {
        $arr = array(
            'APPID'=>'wxd1fcf01a5ba07668',   //微信官方给的，有这个才能用牛逼功能
            'APPSECRET'=>'4fb5940d27b44f978eb24d09b486f3b5'  //微信官方给的，有这个才能用牛逼功能
        );

        $ret = new Wxcore($arr);
    }

}
