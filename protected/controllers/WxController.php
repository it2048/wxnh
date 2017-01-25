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
        header("Access-Control-Allow-Origin: *");
        $url = isset($_GET['url'])?$_GET['url']:'';
        $ret = new Wxcore(Yii::app()->params['yum'],'dx');
        $rtn = $ret->getJs('dx');

        $rtn['noncestr'] = "Wm3WZYTPz0wzccnW";
        $rtn['timestamp'] = time();

        $shaa = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%d&url=%s",
            $rtn['ticket'],
            $rtn['noncestr'],$rtn['timestamp'],$url);
        $shaa = sha1($shaa);
        echo json_encode([
            'appId' => Yii::app()->params['yum']['APPID'],
            'timestamp' => $rtn['timestamp'],
            'nonceStr' => $rtn['noncestr'],
            'signature' => $shaa
        ]);
    }

    public function actionUpVoice()
    {
        $id = Yii::app()->request->getParam('voice');
        $ret = new Wxcore(Yii::app()->params['yum'],'dx');
        $rtn = $ret->getMedia($id);

        print_r($rtn);

    }

}
