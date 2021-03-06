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
        $xml = $this->news();
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
                $xml = $this->weixin->outputText("您已验证通过，请点击“面试查询”查看面试结果吧！");
            }else
            {
                RedisTmp::model()->deleteByPk($openid);
                $xml = $this->weixin->outputText("请输入您简历上所填写的手机号，提交后不能更改");
                RedisTmp::setex($openid,1);
            }
        }elseif("emp-ms"==$this->weixin->_postData->EventKey)
        {
            $openid = $this->weixin->_postData->FromUserName."";
            $lst = User::model()->findByPk($openid);
            $mel = WxNewEmployee::model()->find("tel=:tl",array(":tl"=>$lst->tel));
            if(empty($lst->tel))
            {
                $xml = $this->weixin->outputText("请先完成手机验证，谢谢");
            }elseif(empty($mel)||!in_array($mel->stage,TempList::$Stage))
            {
                $xml = $this->weixin->outputText("抱歉，暂时未查询到您的后续面试安排 ");
            }
            else
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
                                $str = sprintf("恭喜“%s”,本轮面试通过，下轮面试暂定于%s 办证当日请微信回复“姓名+拿证日期”，谢谢！",$mel->employee_name,$hook->desc);

                                //$str = sprintf("恭喜“%s”,本轮面试通过，但暂时未查询到您的后续面试安排，请通过微信咨询我们 ",$mel->employee_name);
                            }else
                            {
                                $str = sprintf("恭喜“%s”,本轮面试通过，下轮面试暂定于%s。请至疾控中心办理健康证一张，详情请点击招聘面试/健康证办理，办证当日请微信回复“姓名+拿证日期”，谢谢！",
                                    $mel->employee_name,$hook->desc
                                );
                            }
                        }else
                            $str = sprintf("恭喜“%s”,本轮面试通过，下轮面试暂定于%s 办证当日请微信回复“姓名+拿证日期”，谢谢！",$mel->employee_name,$hook->desc);
                    }
                    elseif($mel->stage=="HR面试通过")
                    {
                        $inte = WxInterview::model()->findAll("brand=:bd and city=:ct and am_time>:tm order by am_time",array(":bd"=>$tk,":ct"=>$mel->city,":tm"=>time()));

                        if(empty($inte))
                        {
                            $str = sprintf("恭喜“%s”,初试通过，我们将尽快为你安排复试！ ",$mel->employee_name);
                        }else
                        {
                            $str = sprintf("恭喜“%s”,初试通过，下轮面试暂定于“%s，%s”，请提前做好相关准备！若参加请回复“姓名+参加”，谢谢！",
                                $mel->employee_name,date('m月d日',$inte[0]->am_time),$inte[0]->am_add);
                        }
                    }elseif($mel->stage=="AM面试通过")
                    {
//                        $inte = WxInterview::model()->findAll("brand=:bd and city=:ct and oje_time>:tm order by oje_time",array(":bd"=>$tk,":ct"=>$mel->city,":tm"=>time()));
//
//                        if(empty($inte))
//                        {
//                            $str = sprintf("恭喜“%s”,复试通过，下轮面试为餐厅试操作，请至疾控中心办理健康证一张，详情请点击招聘面试/健康证办理，办证当日请微信回复“姓名+拿证日期”，谢谢！ ",$mel->employee_name);
//                        }else
//                        {
//                            $str = sprintf("恭喜“%s”,复试通过，下轮面试暂定于“%s和%s”，地点：%s,%s%s餐厅。请至疾控中心办理健康证一张，详情请点击招聘面试/健康证办理，办证当日请微信回复“姓名+拿证日期”，谢谢！",
//                                $mel->employee_name,date('Y-m-d',$inte[0]->oje_time),date('Y-m-d',$inte[0]->oje_time+86400),$inte[0]->oje_add
//                            ,$mel->employee_brand,$inte[0]->oje_ct
//                            );
//
//                        }
                        return $this->news_1();
                    }elseif($mel->stage=="OJE通过")
                    {
//                        $inte = WxInterview::model()->findAll("brand=:bd and city=:ct and dm_time>:tm order by dm_time",array(":bd"=>$tk,":ct"=>$mel->city,":tm"=>time()));
//
//                        if(empty($inte))
//                        {
//                            $str = sprintf("恭喜“%s”,餐厅试操作通过，我们将尽快为你安排终试！ ",$mel->employee_name);
//                        }else
//                        {
//                            $str = sprintf("恭喜“%s”,餐厅试操作通过，下轮面试暂定于“%s、%s”，请提前做好相关准备！",
//                                $mel->employee_name,date('Y-m-d',$inte[0]->dm_time),$inte[0]->dm_add);
//                        }
                        return $this->news_3();
                    }elseif($mel->stage=="DM面试通过")
                    {
//                        $str = sprintf("恭喜“%s”,您已通过百胜储备经理面试，公司将与您电话确认offer事宜，入职需准备的资料有：
//                        健康证，工行卡，身份证，毕业证，寸照",$mel->employee_name);
                        return $this->news_2();
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

    public function getPk($id)
    {
        $model = RedisTmp::model()->findByPk($id);
        return $model->value;
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
        $tmp = $this->getPk($openid); //状态机
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
                        $xml = $this->weixin->outputText("手机验证成功！请于面试次日9:00后点击“面试查询”查看面试结果吧！");
                        RedisTmp::model()->deleteByPk($openid);
                    }
                }
            }else
            {
                $xml = $this->weixin->outputText("号码格式错误，请重新输入");
                $tmp++;
                RedisTmp::setex($openid,$tmp);
            }
        }elseif($tmp>3)
        {
            $xml = $this->weixin->outputText("号码格式错误次数过多，请重新点击绑定菜单");
            RedisTmp::model()->deleteByPk($openid);
        }
        return $xml;
    }

    private function sendMsg($tel,$code)
    {
        $msg = sprintf("您的手机号为：%s 验证码为：%s",$tel,$code);
        file_put_contents('d:/t.log',$msg."\r\n",8);
    }
    /**
     * 单条新闻
     * @return xml对象
     */
    private function news() {
        $text = '在百胜，不看专业，不阅颜值，你行，你就上！未毕业即可提前上岗，与全职人员同工同酬。';
        $posts = array(
            array(
                'title' => '欢迎开启百胜“寻味”之旅！',
                'discription' => $text,
                'picurl' => 'http://mmbiz.qpic.cn/mmbiz/LEpcyJz5evWJqSg5lxwXLsEeWknjef5nLX4kbM1Jy6pGr7m1TsdibDPVIDDoWkkibS5uUChZABIu0s6aiclZva4ZA/640?wx_fmt=jpeg&tp=webp&wxfrom=5',
                'url' => 'http://mp.weixin.qq.com/s?__biz=MzA3MDA3OTkwOA==&mid=400843770&idx=1&sn=9690927bf22cd2c105288c5867569924#rd',
            )
        );
        return $this->weixin->outputNews($posts);
    }

    private function news_1()
    {
        $text = '恭喜您顺利通过之前的面试环节！接下来，您将参加百胜甄选营运管理人才的特有测试环节-----营运试操作。';
        $posts = array(
            array(
                'title' => 'OJE通关宝典 ',
                'discription' => $text,
                'picurl' => 'http://mmbiz.qpic.cn/mmbiz/LEpcyJz5evWJqSg5lxwXLsEeWknjef5nHwE7tb9bYjMEgVWenicf6LfibXdKwmZpQtTQlaXUnVWJ50A9ia8juhibjw/640?wx_fmt=jpeg&tp=webp&wxfrom=5',
                'url' => 'http://mp.weixin.qq.com/s?__biz=MzA3MDA3OTkwOA==&mid=401090555&idx=1&sn=7bb9445ce78af6859ad5f6d974946280#rd',
            )
        );
        return $this->weixin->outputNews($posts);
    }

    private function news_2()
    {
        $text = '很高兴地通知您，您被聘用为本公司的餐厅储备经理（已大学毕业）/学生直通车储备经理（在校应届生）。';
        $posts = array(
            array(
                'title' => '欢迎加入百胜大家庭！ ',
                'discription' => $text,
                'picurl' => 'http://mmbiz.qpic.cn/mmbiz/LEpcyJz5evX5YQ9xGdr2icF9ubIXkbrW5mry8ftcfFPiapiazdicOQEqBY74YXPjbkoGG7LsLfFf9nRy99p7zk8COA/640?wx_fmt=jpeg&tp=webp&wxfrom=5',
                'url' => 'http://mp.weixin.qq.com/s?__biz=MzA3MDA3OTkwOA==&mid=401131604&idx=1&sn=20f42ff5a05830914349446ac4270f1b#rd',
            )
        );
        return $this->weixin->outputNews($posts);
    }

    private function news_3()
    {

        $text = '知彼知已，百战不殆；不知彼知已，一胜一负；不知彼不知已，每战必殆！《孙子兵法.谋攻篇》';
        $posts = array(
            array(
                'title' => '终试秘籍 ',
                'discription' => $text,
                'picurl' => 'http://mmbiz.qpic.cn/mmbiz/LEpcyJz5evX5YQ9xGdr2icF9ubIXkbrW5xS9XWCmOW4EomicuOaRzBs9h0wiaaEuTOK8r11OjHwl8tcALzibErzEicw/640?wx_fmt=jpeg&tp=webp&wxfrom=5',
                'url' => 'http://mp.weixin.qq.com/s?__biz=MzA3MDA3OTkwOA==&mid=401072370&idx=1&sn=36d690b04e0dae9c431ece36471fc644#rd',
            )
        );
        return $this->weixin->outputNews($posts);
    }
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