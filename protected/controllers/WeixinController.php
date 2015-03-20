<?php

/*
 * 监听微信用户请求，并做出反馈；例如用户关注事件，扫描事件，回复事件等。
 * 
 */

/**
 *  WeixinController class file.
 *
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class WeixinController extends CController{
    
    private $weixin = ""; //存储微信接口对象
    public function init()
    {
         $this->layout = "//layouts/xml";
    }

    public function actionIndex()
    {
        $xmlName = "";
        //$GLOBALS['HTTP_RAW_POST_DATA'] = wxtestdata::$receive;
        $this->weixin = new Wxmessage(Yii::app()->params['weixin']);
        //如果不为post则需要验证权限
        if (!Yii::app()->request->isPostRequest)
        {
            $echostr = Yii::app()->request->getParam('echostr');
            $signature = Yii::app()->request->getParam('signature');
            $timestamp = Yii::app()->request->getParam('timestamp');
            $nonce = Yii::app()->request->getParam('nonce');
            if($this->weixin->checkSignature($signature,$timestamp,$nonce))
            {
                echo $echostr;die();
            }

        }
        else
        {
            if(!empty($this->weixin->_postData))
                $xmlName = $this->processRequest();
        }
        $this->render('index',array('name'=>$xmlName));
    }
    
    /**
     * 通过用户回复内容做转发处理
     * @return xml对象 
     */
    private function processRequest() {
        //消息分类处理
        if ($this->weixin->isTextMsg()) {
               Msg::model()->insertReceive($this->weixin->_postData);
               return $this->textAdaptation($this->weixin->_postData->Content);
        }
        else if ($this->weixin->isEventMsg()) {
            //获取事件类型
            $event_name = $this->weixin->getEventType();
            //事件函数存在才处理
            if($event_name!==FALSE&&method_exists($this,"exe".$event_name))
            {        
                $name = "exe".$event_name;
                return $this->$name();
            }      
        }
        else if ($this->weixin->isLocationMsg()) { //获取用户地理位置
            return $this->weixin->outputText("纬度:".$this->weixin->_postData->Latitude." 经度:".$this->weixin->_postData->Longitude);
        }
    }
    
     /**
     * 用户关注时的处理函数
     * @return xml对象 
     */
    private function exeSubscribe()
    {
//        //未关注时扫描二维码处理函数
//        if(!empty($this->weixin->_postData->EventKey))
//        {
//            //$xml = $this->weixin->outputText('二维码参数为:'.$this->weixin->_postData->EventKey);
//        }
//        else
        $xml = $this->weixin->outputText("谢谢关注");
        $usr = new User();
        $usr->insertOne($this->weixin->_postData->FromUserName);
        return $xml;
    }
        
        
    
    /**
     * 用户取消关注时的处理函数
     * @return true or false
     */
    private function exeUnsubscribe()
    {
        $usr = new User();
        $usr->updateOne($this->weixin->_postData->FromUserName);
    }
    
//     /**
//     * 用户跳转时的处理函数
//     * @return xml对象 
//     */
//    private function exeView()
//    {
//        $xml = $this->weixin->outputText("谢谢跳转");
//        return $xml;
//    }

    /**
     * 通过用户回复，返回不同的内容
     * @param string $str 用户回复的内容
     * @return xml对象
     */
    private function textAdaptation($str)
    {
        //用户回复的内容都转小写
        $str = strtolower($str);
        //绑定邮箱
        $xml = $this->blindEmail($str);
        if(empty($xml))
        {
            //$xml = $this->xiaohua();
        }
        return $xml;
    }
    
//    /**
//     * 单条新闻
//     * @return xml对象 
//     */
//    private function news() {
//        $text = 'QQ黄钻、蓝钻、红钻、绿钻或10Q币任选其一';
//        $posts = array(
//            array(
//                'title' => '福利来了',
//                'discription' => $text,
//                'picurl' => 'http://mmbiz.qpic.cn/mmbiz/PH6G9amqVAl40VOFOdDwKfFiaZ4VW5gwPjuV992SN7zXI3OmDriaBjlLSwzmAbLk1gXJTX4WlCMh5nQKQIrGX9Qg/0',
//                'url' => 'http://mp.weixin.qq.com/s?__biz=MjM5OTY1ODc3NA==&mid=200149478&idx=1&sn=217592c4997ccd57704a0dfe58b65d37#rd',
//            )
//        );
//        return $this->weixin->outputNews($posts);
//    }
    
//    /**
//     * 多条新闻
//     * @return xml对象 
//     */
//    private function fulinews() {
//        $text = 'QQ黄钻、蓝钻、红钻、绿钻或10Q币任选其一';
//        $posts = array(
//            array(
//                'title' => '福利来了',
//                'discription' => $text,
//                'picurl' => 'http://mmbiz.qpic.cn/mmbiz/PH6G9amqVAl40VOFOdDwKfFiaZ4VW5gwPjuV992SN7zXI3OmDriaBjlLSwzmAbLk1gXJTX4WlCMh5nQKQIrGX9Qg/0',
//                'url' => 'http://mp.weixin.qq.com/s?__biz=MjM5OTY1ODc3NA==&mid=200149478&idx=1&sn=217592c4997ccd57704a0dfe58b65d37#rd',
//            ),
//            array(
//                'title' => '夏天来了',
//                'discription' => $text,
//                'picurl' => 'http://mmbiz.qpic.cn/mmbiz/PH6G9amqVAl40VOFOdDwKfFiaZ4VW5gwPjuV992SN7zXI3OmDriaBjlLSwzmAbLk1gXJTX4WlCMh5nQKQIrGX9Qg/0',
//                'url' => 'http://mp.weixin.qq.com/s?__biz=MjM5OTY1ODc3NA==&mid=200149478&idx=1&sn=217592c4997ccd57704a0dfe58b65d37#rd',
//            ),
//            array(
//                'title' => '春天来了',
//                'discription' => $text,
//                'picurl' => 'http://mmbiz.qpic.cn/mmbiz/PH6G9amqVAl40VOFOdDwKfFiaZ4VW5gwPjuV992SN7zXI3OmDriaBjlLSwzmAbLk1gXJTX4WlCMh5nQKQIrGX9Qg/0',
//                'url' => 'http://mp.weixin.qq.com/s?__biz=MjM5OTY1ODc3NA==&mid=200149478&idx=1&sn=217592c4997ccd57704a0dfe58b65d37#rd',
//            )
//        );
//        return $this->weixin->outputNews($posts);
//    }
    
//    /**
//     * 发送笑话
//     * @return xml对象 
//     */
//    private function xiaohua() {
//        $text = "";
//        return $this->weixin->outputText($text);
//    }

//    /**
//     * 发送音乐
//     * @return xml对象 
//     */
//    private function music() {
//        $music = array(
//            'title' => 'HiFi音质版-天空之城',
//            'discription' => 'HiFi音质版-天空之城，音响店测试专用，超赞的效果',
//            'musicurl' => 'http://m2.music.126.net/qU7Jow1W7XoAjM_0aSnN-w==/5704266324960072.mp3',
//            'hdmusicurl' => 'http://m2.music.126.net/qU7Jow1W7XoAjM_0aSnN-w==/5704266324960072.mp3'
//        );
//        return $this->weixin->outputMusic($music);
//    }

}
?>