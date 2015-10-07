<?php

/**
 * 登录控制器
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */
class loginController extends Controller {
    //put your code here
    public function __construct() {
        parent::__construct();
    }
    public function indexAction() {
        session_start();
        $_SESSION['info'] = array();
        $this->showTemplate('login/index'); 
    }
    //上传音频文件到public/autio文件夹
    public function upldAction($arr) {
        //从配置文件获取后台登录验证信息
        $userInfo = $this->config("back");
        $user_name = $userInfo['user']; //帐号
        $password = $userInfo['pwd'];  //密码
        //安全保证
        $Safelog = $this->model('Safelog');
        $ip = $this->get_client_ip();
        $row = $Safelog->getRow($ip);
        if(!empty($row))
        {
            //尝试3次，在半小时内
            if($row['times']>$userInfo['times']&&$row['timee']+$userInfo['time']>time())
            {
                echo "1:帐号与密码不匹配";die();
            }else
            {
                if($row['timee']+$userInfo['time']<time()) $Safelog->updateRow($ip,array("timee"=>time(),"times"=>1));
                if($row['times']<=$userInfo['times'])
                {
                    $Safelog->updateRow($ip,array("timee"=>time(),"times"=>($row['times']+1)));
                }
            }
        }
        else
        {
            $Safelog->insertRow(array("ip"=>$ip,"timee"=>time(),"times"=>1));
        }  
        //验证帐号和密码
        if($arr[0] != $user_name||$arr[1] != $password)
        {
            echo "1:帐号与密码不匹配";
        }  else {
            ini_set("session.cookie_lifetime",3600*60);
            session_start();
            $_SESSION['info'] = array("username"=>$user_name,"key"=>md5($user_name.$userInfo['key']));
            echo "0:backstage_index.html";
        }
    } 
    private function get_client_ip() {
        if (@$_SERVER['HTTP_X_REAL_IP']) {//nginx 代理模式下，获取客户端真实IP
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR']; //浏览当前页面的用户计算机的ip地址
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    public function addtagAction(){
        $name = !empty($_POST['name'])? trim($_POST['name']):"";
        $brand = !empty($_POST['brand'])? trim($_POST['brand']):"50";
        $ccity = !empty($_POST['ccity'])? trim($_POST['ccity']):"";
        $jcity = !empty($_POST['jcity'])? trim($_POST['jcity']):"";
        $school = !empty($_POST['school'])? trim($_POST['school']):"";
        $tel = !empty($_POST['tel'])? trim($_POST['tel']):"1";
        $email = !empty($_POST['email'])? trim($_POST['email']):"0";
       
        //判断文章的必填项是否没填
        if(!empty($name)&&!empty($brand)&&
           !empty($ccity)&&!empty($jcity)&&
           !empty($tel)&&!empty($email)&&$this->isEmail($email)&&$this->isMobile($tel))
        {
            $Article = $this->model('Pipi');
            $row = $Article->getRow($tel);
            if(empty($row))
            {
                $artArry = array(
                    "name" =>$name,"brand" => $brand,"ccity" => $ccity,
                    "jcity" => $jcity,"school" => $school,"tel" => $tel,
                    "email" => $email,"tm"=>time()
                );
                if($Article->insertRow($artArry))
                    $msg = "您的简历已经提交成功，请关闭当前页面，返回微信，谢谢！";
                else
                    $msg = "添加简历失败";  
            }else
                $msg = "添加简历成功";
        }
        else
        {
            $msg = "存在必填项为空";
        }
        $data['msg'] = $msg;
        $this->showTemplate('index/add',$data); 
    }
    
    public function addtjAction(){
        $bl_name = !empty($_POST['bl_name'])? trim($_POST['bl_name']):"";  //伯乐名字
        $bl_add = !empty($_POST['bl_add'])? trim($_POST['bl_add']):"50"; //伯乐岗位
        $bl_tel = !empty($_POST['bl_tel'])? trim($_POST['bl_tel']):""; //伯乐电话
        $qlm_name = !empty($_POST['qlm_name'])? trim($_POST['qlm_name']):""; //千里马名字
        $qlm_tel = !empty($_POST['qlm_tel'])? trim($_POST['qlm_tel']):"";  //千里马电话
        $qlm_city = !empty($_POST['qlm_city'])? trim($_POST['qlm_city']):"1"; //千里马城市
       
        //判断文章的必填项是否没填
        if(!empty($bl_name)&&!empty($bl_add)&&
           !empty($bl_tel)&&!empty($qlm_name)&&
           !empty($qlm_tel)&&!empty($qlm_city)&&$this->isMobile($qlm_tel)&&$this->isMobile($bl_tel))
        {
            $Article = $this->model('Pipitj');
            $artArry = array(
                "bl_name" =>$bl_name,"bl_add" => $bl_add,"bl_tel" => $bl_tel,
                "qlm_name" => $qlm_name,"qlm_tel" => $qlm_tel,"qlm_city" => $qlm_city,
                "tm"=>time()
            );
            if($Article->insertRow($artArry))
                $msg = "您的简历已经提交成功，请关闭当前页面，返回微信，谢谢！";
            else
                $msg = "添加简历失败"; 
        }
        else
        {
            $msg = "存在必填项为空";
        }
        $data['msg'] = $msg;
        $this->showTemplate('index/add',$data); 
    }
    
    /**
     * 验证eamil
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public static function isEmail($value,$match='/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i'){
        $v = trim($value);
        if(empty($v)) 
            return false;
        return preg_match($match,$v);
    }
    /**
     * 验证手机
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isMobile($value,$match='/^[(86)|0]?(13\d{9})|(15\d{9})|(18\d{9})$/'){
        $v = trim($value);
        if(empty($v)) 
            return false;
        return preg_match($match,$v);
    }
}
