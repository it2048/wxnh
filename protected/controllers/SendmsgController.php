<?php

/*
 * Send message to user
 * 
 */

/**
 *  SendmsgController class file.
 *
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class SendmsgController extends AdminSet{
    //put your code here
    
    /**
	 * 列表所有信息
	 */
	public function actionIndex()
	{
        //先获取当前是否有页码信息
        $pages['pageNum'] = Yii::app()->getRequest()->getParam("pageNum", 1); //当前页
        $pages['countPage'] = Yii::app()->getRequest()->getParam("countPage", 0); //总共多少记录
        $pages['numPerPage'] = Yii::app()->request->getParam("numPerPage",30); // 每页显示多少条

        $pages['send_id'] = Yii::app()->getRequest()->getParam("send_id", ""); //按发送方id
        $pages['receive_id'] = Yii::app()->getRequest()->getParam("receive_id", ""); //按接收方id
        $pages['content'] = Yii::app()->getRequest()->getParam("content", ""); //按文档like匹配
        $pages['tmstart'] = Yii::app()->getRequest()->getParam("tmstart", ""); //按开始时间
        $pages['tmstop'] = Yii::app()->getRequest()->getParam("tmstop", ""); //按结束时间
        
        //构造SQL
        $criteria = new CDbCriteria;
        if(!empty($pages['send_id'])) $criteria->addCondition("send_id='".$pages['send_id']."'"); 
        if(!empty($pages['receive_id'])) $criteria->addCondition("receive_id='".$pages['receive_id']."'");
        if(!empty($pages['content'])) $criteria->addCondition("content like '%".$pages['content']."%'");
        
        if(!empty($pages['tmstart'])) $criteria->addCondition("tm>'".strtotime($pages['tmstart'])."'");
        if(!empty($pages['tmstop'])) $criteria->addCondition("tm<='".strtotime($pages['tmstop'])."'"); //大于发送时间，小于等于发送时间
        
        $criteria->limit = $pages['numPerPage'];
        $criteria->offset = $pages['numPerPage'] * ($pages['pageNum'] - 1);
        $criteria->order = " tm DESC";
        if (empty($pages['countPage']))
            $pages['countPage'] = Msg::model()->count($criteria);
        $msgList = Msg::model()->findAll($criteria);

        //通过open_id获取用户名字或者昵称
        $opidList = array();
        $nameList = array();
        foreach ($msgList as $value) {
            array_push($opidList,$value['receive_id'],$value['send_id']);
        }
        $crit = new CDbCriteria;
        $crit->addInCondition('open_id',array_unique($opidList));
        $usrList = User::model()->findAll($crit);
        foreach ($usrList as $val)
        {
            $nameList[$val->open_id] = $val->name==""?$val->nickname:$val->name;
        }
        $nameList[Yii::app()->params['wxid']] = "管理员";
        $this->renderPartial('index', array(
            'msgList' => $msgList,
            'pages' => $pages,
            'nameList'=>$nameList
            ), false, true);
	}
    /**
     * 聊天页面
     */
    public function actionSend()
    {
        $sendid = Yii::app()->getRequest()->getParam("send_id", ""); //按发送方id
        $receiveid = Yii::app()->getRequest()->getParam("receive_id", ""); //按发送方id
        
        if(empty($sendid)||empty($receiveid))
        {
            echo "编号不能为空";
        }else
        {
            
            $sendid = $sendid==Yii::app()->params['wxid']?$receiveid:$sendid;
            $usr = User::model()->findByPk($sendid);

            $tmp = "";
            if(!empty($usr['name'])) $tmp = $usr['name'];
            else if(!empty($usr['nickname']))$tmp = $usr['nickname'];
            else $tmp = $usr['open_id'];
            
            //构造SQL
            $criteria = new CDbCriteria;
            $criteria->addCondition("send_id='".$sendid."' or receive_id='".$sendid."'"); 
            $criteria->order = " tm ASC";
            $msgList = Msg::model()->findAll($criteria);
            $msgStr = "";
            $max = 0;
            foreach ($msgList as $value) {
                 $max = $value['tm']>$max?$value['tm']:$max;
                 $user = $value['send_id'] ==  $sendid?$tmp:"我";
                 $msgStr .=  sprintf("%s: %s \n\n",$user,$value['content']);
            }
            $this->renderPartial('send',array('msgStr' => $msgStr,"usr"=>$usr,"tname"=>$tmp,"max"=>$max));
        }
    }
    /**
     * 发送消息事件
     */
    public function actionSendUsr()
	{
            $msg =$this->msgcode();
            $sendid = Yii::app()->getRequest()->getParam("open_id", ""); //发送的id
            $content = Yii::app()->getRequest()->getParam("content", ""); //发送的内容
            $ret = new Wxcore(Yii::app()->params['weixin']);
            $bl = $ret->sendText(array("userid"=>$sendid,"content"=>$content));
            if($bl)
            { 
                $arr = array(
                    "ToUserName"=>$sendid,
                    "CreateTime"=>time(),
                    "MsgType"=>"text",
                    "Content"=>$content,
                    "FromUserName"=>Yii::app()->params['wxid'],
                );
                Msg::model()->insertSend($arr);
                $msg['code'] = 0;
                $msg['obj'] = time();
            }
            echo json_encode($msg);
	}
     /**
     * 接收消息事件
     */
    public function actionSendMe()
	{
            $msg = $this->msgcode();
            $sendid = Yii::app()->getRequest()->getParam("open_id", ""); //发送的id
            $lst = Yii::app()->getRequest()->getParam("lst", ""); //最后的时间戳
            $tname = Yii::app()->getRequest()->getParam("tname", ""); //发送的内容
            $criteria = new CDbCriteria;
            $criteria->addCondition("send_id='".$sendid."' and tm > '".$lst."'");
            $msgList = Msg::model()->findAll($criteria);
            if(!count($msgList)==0)
            {
                $msgStr = "";
                $max = 0;
                foreach ($msgList as $value) {
                     $max = $value['tm']>$max?$value['tm']:$max;
                     $msgStr .=  sprintf("%s: %s \n\n",$tname,$value['content']);
                }
                $msg['code'] = 0;
                $msg['obj']["tm"] = $max;
                $msg['obj']["txt"] = $msgStr;
            }
            echo json_encode($msg);
	}
}

?>