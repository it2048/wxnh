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
        $ret = new Wxcore(Yii::app()->params['yum'],'yum');
        $rtn = $ret->getJs('yum');

        $rtn['noncestr'] = "Wm3WZYTPzyumccnW";
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
        $msg = ['code'=>1,'msg'=>'失败','data'=>null];
        $voice = Yii::app()->request->getParam('vid');

        if(!empty($voice) && strpos($voice,".") === false)
        {
            $ret = new Wxcore(Yii::app()->params['yum'],'yum');
            $rtn = $ret->getMedia($voice);

            $filename = dirname(Yii::app()->basePath).'/public/'.$voice.".amr";
            if(file_put_contents($filename,$rtn))
            {
                $msg = ['code'=>0,'msg'=>'成功','data'=>[
                    'url' => Yii::app()->request->hostInfo.'/public/'.$voice.".amr",
                    'vid' => $voice
                ]];
            }else
            {
                $msg = ['code'=>2,'msg'=>'文件存储失败','data'=>$rtn];
            }
        }
        echo json_encode($msg);
    }

    /**
     * 获取录音
     */
    public function actionGetVoice()
    {
        $msg = ['code'=>1,'msg'=>'失败','data'=>null];
        $voice = Yii::app()->request->getParam('vid');

        if(!empty($voice) && strpos($voice,".") === false)
        {

            $filename = dirname(Yii::app()->basePath).'/public/'.$voice.".amr";
            if(file_exists($filename))
            {
                $msg = ['code'=>0,'msg'=>'成功','data'=>[
                    'url' => Yii::app()->request->hostInfo.'/public/'.$voice.".amr",
                    'vid' => $voice
                ]];
            }else
            {
                $msg = ['code'=>2,'msg'=>'录音文件不存在','data'=>null];
            }
        }
        echo json_encode($msg);
    }
}
