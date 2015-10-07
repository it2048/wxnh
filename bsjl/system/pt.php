<?php

/**
 *  linkController class file.
 *
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class Pt extends Controller {
    public function __construct() {
        parent::__construct();
        session_start();
         //从配置文件获取后台登录验证信息
        $userInfo = $this->config("back");
        $user_name = $userInfo['user']; //帐号
        if(!isset($_SESSION['info']['username'])||$_SESSION['info']['username']!=$user_name||$_SESSION['info']["key"]!= md5($user_name.$userInfo['key']))
        {
            Application::showError("壮士，你走错地方了……");
        }
    }
    public function getImg(){
        require_once './lib/jdbck/JingdongStorageService.php';
        $ACCESS_KEY = '792fc3d8aeb34caba86a94b4640f489c'; // 请在此处输入您的AccessKey
        $ACCESS_SECRET =  '0707fde69283478ea046223743160e8dqoP51BMU'; // 请在此处输入您的AccessSecret
        return new JingdongStorageService($ACCESS_KEY,$ACCESS_SECRET);
    }
}