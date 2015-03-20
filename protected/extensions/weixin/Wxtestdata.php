<?php

/*
 * 测试数据，发布的时候会删除
 * 
 */

/**
 *  wxtestdata class file.
 *
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class wxtestdata {
    //位置信息
    public static $location = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>1351776360</CreateTime>
<MsgType><![CDATA[location]]></MsgType>
<Location_X>23.134521</Location_X>
<Location_Y>113.358803</Location_Y>
<Scale>20</Scale>
<Label><![CDATA[位置信息]]></Label>
<MsgId>1234567890123456</MsgId>
</xml>';
    //菜单事件
    public static $menu ='<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[FromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[CLICK]]></Event>
<EventKey><![CDATA[EVENTKEY]]></EventKey>
</xml>';
    
    //二维码扫描事件
    public static $ticket = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[o_oABj-ZF7H_7g7cCsVoiqU7Z020]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[SCAN]]></Event>
<EventKey><![CDATA[qrscene_9930]]></EventKey>
<Ticket><![CDATA[TICKET]]></Ticket>
</xml>';
    
    //关注事件
    public static $subscribe = '<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[xiongfanglei2]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
</xml>';
        //发送信息
    public static $receive = '<xml>
 <ToUserName><![CDATA[toUser]]></ToUserName>
 <FromUserName><![CDATA[fromUser]]></FromUserName> 
 <CreateTime>1348831860</CreateTime>
 <MsgType><![CDATA[text]]></MsgType>
 <Content><![CDATA[this is a test]]></Content>
 <MsgId>1234567890123456</MsgId>
 </xml>';
    
}

?>