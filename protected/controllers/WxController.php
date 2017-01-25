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
        header("Access-Control-Allow-Origin: *");

        $msg = ['code'=>1,'msg'=>'失败','data'=>null];
        $voice = Yii::app()->request->getParam('vid');

        if(!empty($voice) && strpos($voice,".") === false)
        {
            $ret = new Wxcore(Yii::app()->params['yum'],'yum');
            $rtn = $ret->getMedia($voice);

            $file = dirname(Yii::app()->basePath).'/public/voi/'.$voice.".amr";
            $filename = dirname(Yii::app()->basePath).'/public/voi/'.$voice.".mp3";
            if(file_put_contents($file,$rtn))
            {
                $command = "/usr/local/bin/ffmpeg -i $file  $filename";
                system($command,$error);
                $msg = ['code'=>0,'msg'=>'成功','data'=>[
                    'url' => Yii::app()->request->hostInfo.'/wx/public/voi/'.$voice.".amr",
                    'vid' => $voice
                ]];
            }else
            {
                $msg = ['code'=>2,'msg'=>'文件存储失败','data'=>$rtn];
            }
        }
        $this->tlog($msg);
        echo json_encode($msg);
    }

    /**
     * 获取录音
     */
    public function actionGetVoice()
    {
        header("Access-Control-Allow-Origin: *");

        $msg = ['code'=>1,'msg'=>'失败','data'=>null];
        $voice = Yii::app()->request->getParam('vid');

        if(!empty($voice) && strpos($voice,".") === false)
        {
            $filename = dirname(Yii::app()->basePath).'/public/voi/'.$voice.".amr";
            if(file_exists($filename))
            {
                $msg = ['code'=>0,'msg'=>'成功','data'=>[
                    'url' => Yii::app()->request->hostInfo.'/wx/public/voi/'.$voice.".amr",
                    'vid' => $voice
                ]];
            }else
            {
                $msg = ['code'=>2,'msg'=>'录音文件不存在','data'=>null];
            }
        }
        $this->tlog($msg);
        echo json_encode($msg);
    }
    private function tlog($msg){
        $log = sprintf("voice-%d: url|%s rtn|%s tm:%s \r\n",$msg['code'],
            Yii::app()->request->getUrl(),$msg['msg'],date('Y-m-d H:i:s'));
        @file_put_contents(dirname(Yii::app()->basePath).'/t.log',$log,8);
    }
}
