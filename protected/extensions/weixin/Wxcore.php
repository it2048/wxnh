<?php

/*
 * 将需要用户提供认证参数的操作封装在此类，
 * 例如菜单，二维码等
 */

/**
 *  wxmenu class file.
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class Wxcore {
    
    const QR_SCENE = 1;  //临时二维码
    const QR_LIMIT_SCENE = 2;  //永久二维码

    private $_ACCESS_TOKEN = "";
    /**
     * 构造函数，初始化认证参数。需要用到redis缓存。
     * @param Array $wx 包含微信接口调用的3个参数，在yii配置文件中查看。
     */
    public function __construct($wx) {
        $this->_ACCESS_TOKEN = Yii::app()->redis->getClient()->get("access_token");
        if(empty($this->_ACCESS_TOKEN))
        {
            //微信认证参数获取接口，该认证参数30分钟失效
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $wx['APPID'] . "&secret=" . $wx['APPSECRET'];
            $content = file_get_contents($url);
            $ret = json_decode($content, true);
            if (!array_key_exists('errcode', $ret)) {
                $this->_ACCESS_TOKEN = $ret['access_token']; 
                //微信官方最长时间为7200秒
                Yii::app()->redis->getClient()->setex("access_token",7000,$ret['access_token']);
            }
        }
    }

    /**
     * POST data
     * @param  string $url 远程接口地址
     * @param array $data 请求附带的数据
     * @return json 微信服务器传回的数据
     */
    public function curl_post($url, $data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //百度bae为1会报错
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);
        }
        curl_close($curl);
        return $tmpInfo;
    }

    /**
     * 创建菜单
     * @param json $menu 菜单定义参数
     * @return true or false
     */
    public function createMenu($menu) {
        
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $this->_ACCESS_TOKEN;
        $content = $this->curl_post($url, $menu);
        $ret = json_decode($content, true);
        if ($ret['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取当前菜单样式
     * @return menu in json,or false
     */
    public function getMenu() {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=" . $this->_ACCESS_TOKEN;
        $content = file_get_contents($url);
        if (strpos($content, 'errcode') === false) {
            return $content;
        } else {
            return false;
        }
    }

    /**
     * 删除当前菜单
     * @return true or false
     */
    public function deleteMenu() {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=" . $this->_ACCESS_TOKEN;
        $content = file_get_contents($url);
        $ret = json_decode($content, true);
        if ($ret['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 获取二维码
     * 
     * @param int $type 二维码类型,1为临时，2为永久
     * @param string $scene type为临时时为32位字无符号整数，否则为纯数字
     * @param int $seconds 秒数 
     */
    public function ceateTicket($type,$scene,$seconds)
    {
        $jsn = '';
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=". $this->_ACCESS_TOKEN;
        if($type == self::QR_LIMIT_SCENE)
        {
            $jsn = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$scene.'}}}';
        }else if($type == self::QR_SCENE)
        {
            $jsn = '{"expire_seconds": '.$seconds.', "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene.'}}}';
        }
        $content = $this->curl_post($url, $jsn);
        $ret = json_decode($content, true);
        if (array_key_exists('errcode',$ret) === false) {
            return $ret;
        } else {
            return false;
        }
    }
    
    /**
     * 通过二维码唯一标识获取二维码图片地址
     * @param string $ticket 二维码唯一标识
     * @return 二维码地址
     */
    public function getTicket($ticket)
    {
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
        return $url;
    }
    
    /**
     * 获取所有分组列表
     * @return array or false
     */
    public function getGroup()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token='. $this->_ACCESS_TOKEN;
        $content = file_get_contents($url);
        $ret = json_decode($content, true);
        if (array_key_exists('errcode',$ret) === false) {
            return $ret;
        } else {
            return false;
        }
    }   
    
    /**
     * 获取某个用户的分组
     * @param string $usrid 用户微信id
     * @return array or false
     */
    public function getUsrgroup($usrid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/getid?access_token='. $this->_ACCESS_TOKEN;
        $jsn = '{"openid":"'.$usrid.'"}';
        $content = $this->curl_post($url, $jsn);
        $ret = json_decode($content, true);
        if (array_key_exists('errcode',$ret) === false) {
            return $ret;
        } else {
            return false;
        }

    }
    
    /**
     * 创建新的分组
     * @param string $name 分组名称
     * @return array or false
     */
    public function createGroup($name)
    {
        if(strlen($name)>=30){ throw new Exception('分组名称不能大于30个字符');};
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token='. $this->_ACCESS_TOKEN;
        $jsn = '{"group":{"name":"'.$name.'"}}';
        $content = $this->curl_post($url, $jsn);
        $ret = json_decode($content, true);
        if (array_key_exists('errcode',$ret) === false) {
            return $ret;
        } else {
            return false;
        }

    }
    /**
     * 更新分组
     * @param int $groupid 组编号
     * @param string $name 分组名称
     * @return true or false
     */
    public function updateGroup($groupid,$name)
    {
        if(strlen($name)>=30){ throw new Exception('分组名称不能大于30个字符');};
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/update?access_token='. $this->_ACCESS_TOKEN;
        $jsn = '{"group":{"id":'.$groupid.',"name":"'.$name.'"}}';
        $content = $this->curl_post($url, $jsn);
        $ret = json_decode($content, true);
        if ($ret['errcode'] == 0) {
            return true;
        } else {
            return false;
        }

    }
    
    /**
     * 移动用户到指定分组
     * @param string $usrid 用户openid
     * @param int $groupid 组编号
     * @return true or 错误数据
     */
    public function transGroup($usrid,$groupid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token='. $this->_ACCESS_TOKEN;
        $jsn = '{"openid":"'.$usrid.'","to_groupid":'.$groupid.'}';
        $content = $this->curl_post($url, $jsn);
        $ret = json_decode($content, true);
        if ($ret['errcode'] == 0) {
            return true;
        } else {
            return $ret['errmsg'];
        }

    }
    
    /**
     * 获取用户信息
     * @param string $usrid 用户openid
     * @param string $lang 需要返回那种语言
     * @return array or false
     */
    public function getUsrinfo($usrid,$lang="zh_CN")
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='. $this->_ACCESS_TOKEN.'&openid='.$usrid.'&lang='.$lang;
        $content = file_get_contents($url);
        $ret = json_decode($content, true);
        if (array_key_exists('errcode',$ret) === false) {
            return $ret;
        } else {
            return false;
        }
    }   
    
    /**
     * 获取所有关注者
     * @return array or false
     */
    public function getAllusr()
    {
        
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='. $this->_ACCESS_TOKEN.'&next_openid=';
        $content = file_get_contents($url);
        $ret = json_decode($content, true);
        if (array_key_exists('errcode',$ret) === false) {
            //接口每次只能取10000的数据
            $i = ceil($ret['total']/10000.0)-1;
            $open = $ret['data']['openid'];
            while($i>0)
            {
                $cnt = file_get_contents($url.$ret['next_openid']);
                $retn = json_decode($cnt, true);
                if (array_key_exists('errcode',$retn) === false) {
                        $open .= $retn['data']['openid'];
                }
                $i--;
            }
            return $open;
        } else {
            return false;
        }
    }   
    
    /**
     * 向用户发送文本信息
     */
    public function sendText($arr)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='. $this->_ACCESS_TOKEN;
        $jsn = '{"touser":"'.$arr['userid'].'","msgtype":"text","text":{"content":"'.$arr['content'].'"}}';
        $content = $this->curl_post($url, $jsn);
        $ret = json_decode($content, true);
        if ($ret['errcode'] == 0) {
            return true;
        } else {
            return $ret['errmsg'];
        }
    }
    
}

?>