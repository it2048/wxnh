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

    /**
     * 用户点击菜单时的处理函数
     * @return xml对象
     */
    private function exeClick()
    {
        if("emp_blind"==$this->weixin->_postData->EventKey)
        {
            $usr = new User();
            $openid = $this->weixin->_postData->FromUserName."";
            $lst = $usr->findByPk($openid);
            if($lst->type==2&&!empty($lst->employee_id))
            {
                $xml = $this->weixin->outputText("您已验证通过，不需要再次验证");
            }else
            {
                $rds = Yii::app()->redis->getClient();
                $rds->del($openid);
                $rds->del($openid."tel");
                $rds->del($openid."ext");
                $xml = $this->weixin->outputText("请输入您的手机号或者身份证号");
                $rds->setex($openid,600,1);
            }
        }
        return $xml;
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
        $xml = $this->blindTel($str);
        if(empty($xml))
        {
            //$xml = $this->xiaohua();
        }
        return $xml;
    }

    /**
     * 生成随机码
     * @param int $length 随机码字数
     * @return 字符串
     */
    private function get_password( $length = 8 )
    {
        $str = substr(md5(time()), 0, $length);
        return $str;
    }
    /**
     * 绑定用户邮箱
     * @param string $str 用户回复的内容
     * @param int $tmp 状态机
     * @return 字符串
     */
    private function blindTel($str) {
        $xml = "";
        $openid = $this->weixin->_postData->FromUserName . "";
        //开启redis
        $rds = Yii::app()->redis->getClient();
        $tmp = $rds->get($openid); //状态机
        //状态机为0-3标识处于 输入邮箱阶段
        if ($tmp > 0 && $tmp <= 3) {
            //验证电话或者身份证格式，当然还需要验证邮件是否发送成功
            $len = strlen($str);
            if ($len==11||$len==18) {
                file_put_contents('d:/t.log',$str."\r\n",8);
                $model = WxEmployee::model()->find("tel=:tel or cid=:cd",array(":tel"=>$str,":cd"=>$str));
                if(empty($model)||empty($model->tel))
                {
                    $xml = $this->weixin->outputText("您非内部员工，无法验证！");
                    if (++$tmp == 4) {
                        $rds->del($openid);
                    } else {
                        $rds->setex($openid, 600, $tmp);
                    }
                }
                else{

                    $pl = User::model()->find("employee_id='{$model->emp_id}'");
                    if(!empty($pl)&&$pl->type==2)
                    {
                        $xml = $this->weixin->outputText("改号码已被绑定，请重试");
                        if (++$tmp == 4) {
                            $rds->del($openid);
                        } else {
                            $rds->setex($openid, 600, $tmp);
                        }
                    }else{
                        $ext = $this->get_password(4); //随机码
                        $rds->setex($openid, 600, 4);
                        //这里保证Email与ext同时存在
                        $rds->setex($openid . "tel", 600, $str); //缓存邮箱
                        $rds->setex($openid . "ext", 600, $ext); //缓存随机码
                        $this->sendMsg($model->tel,$ext);
                        $xml = $this->weixin->outputText("验证码已发送至您的手机，请回复验证码完成绑定！（若5分钟内未收到验证码请重新点击\"员工绑定\"菜单）");
                    }
                }
            } else {
                //状态机超过容错次数就会置空redis缓存
                if (++$tmp == 4) {
                    $rds->del($openid);
                    $xml = $this->weixin->outputText("输入格式错误，请重新点击“验证”菜单");
                } else {
                    $rds->setex($openid, 600, $tmp);
                    $xml = $this->weixin->outputText("输入格式错误，请重新输入，还有" . (4 - $tmp) . "次机会");
                }
            }
        } else if ($tmp > 3 && $tmp <= 6) { //状态机为4-6标识处于输入验证码阶段
            $ext = $rds->get($openid . "ext");
            if (!empty($ext) && $str == $ext) {

                $tel = $rds->get($openid."tel");
                $model = WxEmployee::model()->find("tel=:tel or cid=:cd",array(":tel"=>$tel,":cd"=>$tel));
                if(!empty($model))
                {
                    $user = new User();
                    //判断用户微信id是否存在
                    $postid = $user->findByPk($openid);
                    //更新记录
                    $postid->tel = $model->tel;
                    $postid->email = $model->email;
                    $postid->type = 2;
                    $postid->name = $model->emp_name;
                    $postid->employee_id = $model->emp_id;
                    if($postid->save())
                    {
                        $rds->del($openid);
                        $rds->del($openid . "tel");
                        $rds->del($openid . "ext");
                        $xml = $this->weixin->outputText("验证成功");
                    }else
                    {
                        file_put_contents('d:/t.log',print_r($postid->getErrors(),true),8);
                        $xml = $this->weixin->outputText("验证超时");
                    }

                }else
                {
                    $xml = $this->weixin->outputText("非内部员工，验证失败");
                }
            } else {
                //状态机超过容错次数就会置空redis缓存
                if (++$tmp >= 7) {
                    $rds->del($openid);
                    $rds->del($openid . "tel");
                    $rds->del($openid . "ext");
                    $xml = $this->weixin->outputText("验证码输入错误次数过多，请重新点击“验证邮箱”菜单");
                } else {
                    //这里要保证openid，Eamil与ext的缓存时间一致
                    $rds->setex($openid, $rds->ttl($openid), $tmp);
                    $xml = $this->weixin->outputText("验证码错误，请重新输入，还有" . (7 - $tmp) . "次机会");
                }
            }
        }
        return $xml;
    }

    private function sendMsg($tel,$code)
    {
        $msg = sprintf("您的手机号为：%s 验证码为：%s",$tel,$code);
        file_put_contents('d:/t.log',$msg."\r\n",8);
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