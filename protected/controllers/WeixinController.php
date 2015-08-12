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
        if("emp_tel"==$this->weixin->_postData->EventKey)
        {
            $usr = new User();
            $openid = $this->weixin->_postData->FromUserName."";
            $lst = $usr->findByPk($openid);
            if(!empty($lst->tel))
            {
                $xml = $this->weixin->outputText("您已验证通过，请点击查询按钮");
            }else
            {
                $rds = Yii::app()->redis->getClient();
                $rds->del($openid);
                $xml = $this->weixin->outputText("请输入您简历上所填写的手机号，提交后不能更改");
                $rds->setex($openid,600,1);
            }
        }elseif("emp-ms"==$this->weixin->_postData->EventKey)
        {
            $openid = $this->weixin->_postData->FromUserName."";
            $lst = User::model()->findByPk($openid);
            $mel = WxNewEmployee::model()->find("tel=:tl",array(":tl"=>$lst->tel));
            if(empty($mel)||!in_array($mel->stage,TempList::$Stage))
            {
                $xml = $this->weixin->outputText("抱歉，暂时未查询到您的后续面试安排 ");
            }else
            {
                $brand = Homeconf::model()->findByPk('brand');
                $arr = explode(",",$brand->value);
                $tk = array_search($mel->employee_brand,$arr);
                if($tk===false)
                {
                    $xml = $this->weixin->outputText("抱歉，暂时未查询到您的后续面试安排 ");
                }else
                {
                    $str = "";
                    $hook = WxHook::model()->find("tel=:tl and stage=:stg",array(":tl"=>$mel->tel,":stg"=>$mel->stage));
                    if(!empty($hook))
                    {
                        if($mel->stage=="AM面试通过")
                        {
                            $inte = WxInterview::model()->findAll("brand=:bd and city=:ct and oje_time>:tm order by oje_time",array(":bd"=>$tk,":ct"=>$mel->city,":tm"=>time()));

                            if(empty($inte))
                            {
                                $str = sprintf("恭喜“%s”,本轮面试通过。%s",$mel->employee_name,$hook->desc);

                                //$str = sprintf("恭喜“%s”,本轮面试通过，但暂时未查询到您的后续面试安排，请通过微信咨询我们 ",$mel->employee_name);
                            }else
                            {
                                $str = sprintf("恭喜“%s”,本轮面试通过，下轮面试暂定于%s,餐厅：%s,品牌：%s。请至疾控中心办理健康证一张具体详见链接：XXXXX",
                                    $mel->employee_name,$hook->desc
                                    ,$inte[0]->oje_ct,$mel->employee_brand
                                );
                            }
                        }else
                            $str = sprintf("恭喜“%s”,本轮面试通过。%s",$mel->employee_name,$hook->desc);
                    }
                    elseif($mel->stage=="HR面试通过")
                    {
                        $inte = WxInterview::model()->findAll("brand=:bd and city=:ct and am_time>:tm order by am_time",array(":bd"=>$tk,":ct"=>$mel->city,":tm"=>time()));

                        if(empty($inte))
                        {
                            $str = sprintf("恭喜“%s”,本轮面试通过，但暂时未查询到您的后续面试安排，请通过微信咨询我们 ",$mel->employee_name);
                        }else
                        {
                            $str = sprintf("恭喜“%s”,本轮面试通过，下轮面试暂定于“%s、%s”，请提前做好相关准备！",
                                $mel->employee_name,date('Y-m-d',$inte[0]->am_time),$inte[0]->am_add);
                        }
                    }elseif($mel->stage=="AM面试通过")
                    {
                        $inte = WxInterview::model()->findAll("brand=:bd and city=:ct and oje_time>:tm order by oje_time",array(":bd"=>$tk,":ct"=>$mel->city,":tm"=>time()));

                        if(empty($inte))
                        {
                            $str = sprintf("恭喜“%s”,本轮面试通过，但暂时未查询到您的后续面试安排，请通过微信咨询我们 ",$mel->employee_name);
                        }else
                        {
                            $str = sprintf("恭喜“%s”,本轮面试通过，下轮面试暂定于“%s和%s”，地点：%s,%s%s餐厅。请至疾控中心办理健康证一张，详情请点击招聘面试/健康证办理",
                                $mel->employee_name,date('Y-m-d',$inte[0]->oje_time),date('Y-m-d',$inte[0]->oje_time+86400),$inte[0]->oje_add
                            ,$mel->employee_brand,$inte[0]->oje_ct
                            );

                        }
                    }elseif($mel->stage=="OJE通过")
                    {
                        $inte = WxInterview::model()->findAll("brand=:bd and city=:ct and dm_time>:tm order by dm_time",array(":bd"=>$tk,":ct"=>$mel->city,":tm"=>time()));

                        if(empty($inte))
                        {
                            $str = sprintf("恭喜“%s”,本轮面试通过，但暂时未查询到您的后续面试安排，请通过微信咨询我们 ",$mel->employee_name);
                        }else
                        {
                            $str = sprintf("恭喜“%s”,本轮面试通过，下轮面试暂定于“%s、%s”，请提前做好相关准备！",
                                $mel->employee_name,date('Y-m-d',$inte[0]->dm_time),$inte[0]->dm_add);
                        }
                    }elseif($mel->stage=="DM面试通过")
                    {
                        $str = sprintf("恭喜“%s”,您已通过百胜储备经理面试，公司将与您电话确认offer事宜，入职需准备的资料有：
                        健康证，工行卡，身份证，毕业证，寸照",$mel->employee_name);
                    }
                    $xml = $this->weixin->outputText($str);
                }
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
        if ($tmp >= 1&&$tmp<=3) {
            if(CheckInfo::phone($str))
            {
                $pl = User::model()->find("open_id='{$openid}'");
                if(!empty($pl)&&empty($pl->tel))
                {
                    $pl->tel = $str;
                    if($pl->save())
                    {
                        $xml = $this->weixin->outputText("手机验证成功！请点击“面试查询”查看面试结果吧！");
                        $rds->del($openid);
                    }
                }
            }else
            {
                $xml = $this->weixin->outputText("号码格式错误，请重新输入");
                $tmp++;
                $rds->setex($openid, 600, $tmp);
            }
        }elseif($tmp>3)
        {
            $xml = $this->weixin->outputText("号码格式错误次数过多，请重新点击绑定菜单");
            $rds->del($openid);
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