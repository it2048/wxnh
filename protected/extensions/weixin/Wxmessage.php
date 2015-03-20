<?php
/**
 * 微信消息处理函数，封装了微信接口所有的事件处理接口
 * 
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class Wxmessage
{
    //接收的消息类型
    const MSG_TYPE_TEXT = 'text';       //消息类型_文本消息
    const MSG_TYPE_IMAGE='image'; //消息类型_图片消息
    const MSG_TYPE_LINK='link';  //链接地址
    const MSG_TYPE_LOCATION = 'location'; //用户地理位置
    const MSG_TYPE_EVENT='event';//所有的事件
    
    //发送的消息类型
    const REPLY_TYPE_MUSIC='music'; //回复类型_音乐
    const REPLY_TYPE_TEXT = 'text'; //回复类型_文本
    const REPLY_TYPE_NEWS = 'news'; //回复类型_新闻
    
    private  $APPID = "";  //微信认证参数
    private  $APPSECRET = ""; //微信认证参数
    private  $TOKEN = ""; //微信认证参数
    
    public $_postData; //微信接口传入的数据，可能是用户回复或者用户点击
    /**
     * 构造函数，初始化认证参数。
     * @param Array $wx 包含微信接口调用的3个参数，在yii配置文件中查看。
     */
    public function __construct($wx)
    {
        //TOKEN为必须
        if(!empty($wx['TOKEN']))
            $this->TOKEN = $wx['TOKEN'];
        else
            throw new Exception('Token is required');
        
        if(!empty($wx['APPSECRET']))
            $this->APPSECRET = $wx['APPSECRET'];
        if(!empty($wx['APPID']))
            $this->APPID = $wx['APPID'];
        
        $this->parsePostRequestData();
    }

    /**
     * 判断事件类型
     * @return 处理函数名称或者 FALSE
     */
    public function getEventType(){
        if($this->_postData->MsgType == self::MSG_TYPE_EVENT)
        {
            return ucfirst(strtolower($this->_postData->Event));
        }
        return FALSE;
    }

    /**
     * 检测是否为文本消息
     * @return boolean
     */
    public function isTextMsg()
    {
        return $this->_postData->MsgType == self::MSG_TYPE_TEXT;
    }
    
    /**
     * 检测是否为地址消息
     * @return boolean
     */
    public function isLocationMsg()
    {
        return $this->_postData->MsgType == self::MSG_TYPE_LOCATION;
    }
    
    /**
     * 检测是否为图片消息
     * @return boolean
     */
    public function isImageMsg(){
        return $this->_postData->MsgType == self::MSG_TYPE_IMAGE;
    }

    /**
     * 检测是否为链接
     * @return boolean
     */
    public function isLinkMsg(){
        return $this->_postData->MsgType == self::MSG_TYPE_LINK;
    }
    
    /**
     * 检测是否为事件
     * @return boolean
     */
    public function isEventMsg(){
        return $this->_postData->MsgType == self::MSG_TYPE_EVENT;
    } 
    
    /**
     * 发送文本消息
     * @param string $content 文本内容
     * @return string xml
     */
    public function outputText($content)
    {
        $textTpl = '<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
            </xml>';
    
        $text = sprintf($textTpl, $this->_postData->FromUserName, $this->_postData->ToUserName, time(), self::REPLY_TYPE_TEXT, $content);
        return $text;
    }
    
    /**
     * 发送多文本消息
     * @param arrry $posts 文本数组. 每一项都是一个单独的文本消息数组.
     * @return string xml
     */
    public function outputNews($posts = array())
    {
        $textTpl = '<xml>
             <ToUserName><![CDATA[%s]]></ToUserName>
             <FromUserName><![CDATA[%s]]></FromUserName>
             <CreateTime>%s</CreateTime>
             <MsgType><![CDATA[%s]]></MsgType>
             <ArticleCount>%d</ArticleCount>
             <Articles>%s</Articles>
             <FuncFlag>1<FuncFlag>
         </xml>';
        
        $itemTpl = '<item>
             <Title><![CDATA[%s]]></Title>
             <Discription><![CDATA[%s]]></Discription>
             <PicUrl><![CDATA[%s]]></PicUrl>
             <Url><![CDATA[%s]]></Url>
         </item>';
        
        $items = '';
        foreach ((array)$posts as $p) {
            if (is_array($p))
                $items .= sprintf($itemTpl, $p['title'], $p['discription'], $p['picurl'], $p['url']);
            else
                throw new Exception('$posts data structure wrong');
        }
        
        $text = sprintf($textTpl, $this->_postData->FromUserName, $this->_postData->ToUserName, time(), self::REPLY_TYPE_NEWS,  count($posts), $items);
        return $text;
    }
    
    /**
     * 发送音乐消息
     * @param type $musicpost  
     * @return type
     * @throws Exception 数据结构错误
     */
    public function outputMusic($musicpost){
        $textTpl = '<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType> 
            <Music>%s</Music>
            <FuncFlag>0</FuncFlag>
        </xml>';
        
        $musicTpl = '
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <MusicUrl><![CDATA[%s]]></MusicUrl>
            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
            ';
        $music = '';        
        if (is_array($musicpost)){
            $music .= sprintf($musicTpl, $musicpost['title'], $musicpost['discription'], $musicpost['musicurl'], $musicpost['hdmusicurl']);
        }else{
            throw new Exception('$posts data structure wrong');
        }
        
    
        $text = sprintf($textTpl, $this->_postData->FromUserName, $this->_postData->ToUserName, time(), self::REPLY_TYPE_MUSIC, $music);
        return $text;
         
    }

    /**
     * 接收微信服务器POST来的XML格式消息，转换为SimpleXMLElement对象
     * @return SimpleXMLElement
     */
    public function parsePostRequestData()
    {
        //只处理post消息
        if(!empty($GLOBALS['HTTP_RAW_POST_DATA']))
        {
            $rawData = $GLOBALS['HTTP_RAW_POST_DATA'];
            $data = simplexml_load_string($rawData, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($data !== false)
            {
                $this->_postData = $data;
            }
            return $data;
        }
    }
    
    /**
     * 返回接收的POST数组
     * @return object
     */
    public function getPostData()
    {
        return $this->_postData;
    }
    /**
     * 判断此条消息的真实性,是否由微信发出
     * @param string $signature 三个参数都是微信端通过get方式传递来的数据
     * @return boolean
     */
    public function checkSignature($signature,$timestamp,$nonce)
    {
        $params = array($this->TOKEN, $timestamp, $nonce);
        sort($params);
        $sig = sha1(implode($params));
        return $sig == $signature;
    }
}