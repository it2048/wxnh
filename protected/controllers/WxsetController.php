<?php

/*
 * 微信菜单与二维码的设置类，通过发送POST请求的封装
 * 接口都在该类调用，不需要上传至公网服务器也能操作。
 * 
 */

/**
 *  微信菜单等信息设置
 *
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class WxsetController extends CController{
    
    /**
     * 微信菜单生成
     */
    public function actionIndex() {
        $ret = new Wxcore(Yii::app()->params['weixin']);
        $menuPostData='{
  				 "button":[
					 {	
						  "type":"view",
						  "name":"跳转菜单",
						  "url":"http://www.haoyongwang.com/"
					  },
					  {
						   "type":"click",
						   "name":"电话查询",
						   "key":"V1001_TODAY_SINGER"
					  },
					  {
						   "name":"菜单",
						   "sub_button":[
							{
							   "type":"click",
							   "name":"验证邮箱",
							   "key":"cdxsj_checkemail"
							},
							{
							   "type":"click",
							   "name":"赞一下我们",
							   "key":"V1001_GOOD"
							}]
					   }]
				 }';
         echo $ret->createMenu($menuPostData)?"创建菜单成功":"创建菜单失败";
    }
    public function actionTest() {
        $tp = Yii::app()->redis->getClient();
        /*接下来就可以对该库进行操作了，具体操作方法请参考phpredis官方文档*/
            $ret = $tp->setex("key",600,"qwe");
            if ($ret === false) {
                die($tp->getLastError());
            } else {
                echo $tp->get("key");
            }
        die();
        
    }
    
    /**
     * 二维码生成，并且输出地址
     * 
     */
    public function actionCreateticket()
    {
        $ret = new Wxcore(Yii::app()->params['weixin']);
        $ticket = $ret->ceateTicket($ret::QR_SCENE, "9999", 18000);
        echo "临时二维码：";
        if($ticket)
            echo $ret->getTicket($ticket['ticket']);
        else
            echo 'Error';
    }
    
    /**
     * 获取分组列表
     */
    public function actionGetgroup()
    {
        $ret = new Wxcore(Yii::app()->params['weixin']);
        echo '<pre>';
        print_r($ret->getGroup());
        echo '</pre>';
    }
    
     /**
     * 获取用户所在分组
     * @param string $usr 用户微信帐号唯一标识
     */
    public function actionGetusrgroup()
    {
        $usr = Yii::app()->request->getParam('usr');
        $ret = new Wxcore(Yii::app()->params['weixin']);
        echo '<pre>';
        print_r($ret->getUsrgroup($usr));
        echo '</pre>';
    }
    
    /**
     * 创建用户分组
     * @param string $name 分组名称
     */
    public function actionCreategroup()
    {
        $usr = Yii::app()->request->getParam('name');
        $ret = new Wxcore(Yii::app()->params['weixin']);
        echo '<pre>';
        print_r($ret->createGroup($usr));
        echo '</pre>';
    }
    
    /**
     * 更新分组
     * @param int $id 分组编号
     * @param string $name 分组名称
     */
    public function actionUpdategroup()
    {
        $id = Yii::app()->request->getParam('id');
        $usr = Yii::app()->request->getParam('name');
        $ret = new Wxcore(Yii::app()->params['weixin']);
        echo $ret->updateGroup($id,$usr);
    }
    
     /**
     * 更新用户分组
     * @param string $uid 用户唯一编号
     * @param int $gid 分组编号
     */
    public function actionUpdusrgrp()
    {
        $id = Yii::app()->request->getParam('uid');
        $usr = Yii::app()->request->getParam('gid');
        $ret = new Wxcore(Yii::app()->params['weixin']);
        echo $ret->transGroup($id,$usr);
    }
    
    /**
     * 获取用户基本信息
     * @param string $uid 用户微信编号
     */
    public function actionGetusrinfo() {
        
        $usr = Yii::app()->request->getParam('uid');
        $ret = new Wxcore(Yii::app()->params['weixin']);
        echo '<pre>';
        print_r($ret->getUsrinfo($usr));
        echo '</pre>';
        
    }
    /**
     * 获取所有关注者
     */
    public function actionGetallusr() {
        $ret = new Wxcore(Yii::app()->params['weixin']);
        echo '<pre>';
        print_r($ret->getAllusr());
        echo '</pre>';
        
    }
    /**
     * 将当前所有用户加入数据库
     */
    public function actionInsertusr()
    {
        $ret = new Wxcore(Yii::app()->params['weixin']);
        $usrList = $ret->getAllusr();
        $usr = new User();
        foreach ($usrList as $value) {
            $usr->insertOne($value);
            $usr->setIsNewRecord(TRUE);
        }
        echo '<pre>';
        print_r($ret->getAllusr());
        echo '</pre>';
    }
    /**
     * 更新当前用户的昵称等信息
     */
    public function actionUpdateusr()
    {
        $ret = new Wxcore(Yii::app()->params['weixin']);
        $usr = new User();
        $userinfo = $usr->findAll("type=:type",array("type"=>1));
        foreach ($userinfo as $value) {
            $usrList = $ret->getUsrinfo($value->open_id);
            $grp = $ret->getUsrgroup($value->open_id);

            echo "<pre>";
            print_r($usrList);
            echo "</pre>";
            $usr->updateAll(array('nickname'=>$usrList['nickname'],'group_id'=>$grp['groupid'],'sex'=>$usrList['sex'],'city'=>$usrList['city'],'province'=>$usrList['province'],'country'=>$usrList['country'],"type"=>1),'open_id=:open_id',
                    array(':open_id'=>$value->open_id));
            $usr->setIsNewRecord(TRUE);
        }

    }
    
    public function actionGetredis() {
        
        Yii::app()->redis->getClient()->setex("aa",7000,"123");
        echo Yii::app()->redis->getClient()->get("aa");
        
    }
    
    /**
     * POST data
     * @param  string $url 远程接口地址
     * @param array $data 请求附带的数据
     * @return json 微信服务器传回的数据
     */
    public function actionSendemail() {
        
        $v = "ksgwx";
        $vc = "8f578332b04b1dc12822c3f1c06a0a60";
        $recver = "xiongfanglei@kingsoft.com";
        $subject = "成都西山居微信平台邮箱验证";
        $content = "您的验证码是：123";
        
        $curlPost = array(
            "v" => $v,
            "vc" => $vc,
            "recver" => $recver,
            "subject" => $subject,
            "content" => $content
        );
        $ch = curl_init();//初始化curl
        curl_setopt($ch,CURLOPT_URL,'http://182.139.133.136:808/ksgkefu/public/index.php/ksgemail/sendemailtxt');//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        if(curl_errno($ch)){//出错则显示错误信息
		print_r(curl_error($ch));
        }
        curl_close($ch);
        print_r($data);//输出结果
    }
    
    public function actionIP(){
        
        //http://whois.pconline.com.cn/ipJson.jsp
        
        $ip = file_get_contents("http://whois.pconline.com.cn/ipJson.jsp");
        echo $ip;
    }
        
}

?>