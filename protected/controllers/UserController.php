<?php

class UserController extends AdminSet
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='';


	/**
	 * 列表所有用户
	 */
	public function actionIndex()
	{
        //先获取当前是否有页码信息
        $pages['pageNum'] = Yii::app()->getRequest()->getParam("pageNum", 1); //当前页
        $pages['countPage'] = Yii::app()->getRequest()->getParam("countPage", 0); //总共多少记录
        $pages['orderField'] = Yii::app()->request->getParam("orderField","group_id"); // 排序字段
        $pages['numPerPage'] = Yii::app()->request->getParam("numPerPage",30); // 每页显示多少条
        $pages['orderDirection'] = Yii::app()->request->getParam("orderDirection","DESC"); // 每页显示多少条
        $pages['orderDirection'] = $pages['orderDirection']!=="asc"?" DESC":" ASC";
        
        //构造SQL
        $criteria = new CDbCriteria;
        if (empty($pages['countPage']))
            $pages['countPage'] = User::model()->count($criteria);
        $group = Group::model()->findAll();
        $grpList = array();
        foreach ($group as $value) {
            $grpList[$value['id']] = $value['name'];
        }
        $connection = Yii::app()->db;
        $sql = sprintf("SELECT b.*,a.emp_name FROM wx_user b left join wx_employee a on a.emp_id = b.employee_id limit %d,%d",
            $pages['numPerPage'] * ($pages['pageNum'] - 1),$pages['numPerPage']); //构造SQL

        $usrList = $connection->createCommand($sql)->queryAll();
        $this->renderPartial('index', array(
            'usrList' => $usrList,
            'pages' => $pages,
            'grpList'=>$grpList
            ), false, true);
	}
    /**
     * 通过open_id 抓取所有类型为0的用户详细信息
     * @return json 是否成功
     */
    public function actionUpdateuesrs()
    {
        $msg = $this->msgcode();
        $ret = new Wxcore(Yii::app()->params['weixin']);
        $usr = new User();
        //以关注的用户更新内容
        $userinfo = $usr->findAll("subscribe=:type",array("type"=>1));
        try {
            foreach ($userinfo as $value) {
                $usrList = $ret->getUsrinfo($value->open_id);
                $tpe = $value->type==0?1:$value->type;
                //不关注什么信息都不会有
                if($usrList['subscribe']==0)
                {
                    $usr->updateAll(array("subscribe"=>$usrList['subscribe'],"type"=>$tpe),'open_id=:open_id',array(':open_id'=>$value->open_id));
                }else
                {
                    $grp = $ret->getUsrgroup($value->open_id);
                    $usr->updateAll(array('nickname'=>$usrList['nickname'],'group_id'=>$grp['groupid'],'sex'=>$usrList['sex'],'city'=>$usrList['city'],'province'=>$usrList['province'],
                    'country'=>$usrList['country'],"subscribe"=>$usrList['subscribe'],"type"=>$tpe),'open_id=:open_id',array(':open_id'=>$value->open_id));
                }
                $usr->setIsNewRecord(TRUE);
            }
            $this->msgsucc($msg);
        } catch (Exception $exc) {
                 $msg['msg'] = "微信端口连接错误";
        }
        echo json_encode($msg);
        Yii::app()->end();
    }
    
     /**
     * 获取所有用户，不存在则存储，存在则不处理
     */
    public function actionGetNewUser()
    {
        $msg = $this->msgcode();
        $ret = new Wxcore(Yii::app()->params['weixin']);
        $usr = User::model()->findAll(array('select' => 'open_id'));
        //存储所有的open_id
        $usarr = array();
        foreach ($usr as $val) {
            $usarr[] = $val['open_id'];
        }
        //以关注的用户更新内容
        $userinfo = $ret->getAllusr();
        $userCt = count($userinfo);
        $tbname = User::model()->tableName();
        $insert = "INSERT INTO ".$tbname."(open_id,subscribe)VALUES"; //构造SQL
        $isttmp = "";
        for ($i = 0; $i < $userCt; $i++) {
            if(!in_array($userinfo[$i], $usarr))
            {
                $isttmp .= sprintf("('%s',%d),",$userinfo[$i],1);
            }
        }
        if($isttmp!="")
        {
            $insert .= $isttmp;
            $insert = rtrim($insert,",");
            $connection = Yii::app()->db;
            $insertCom = $connection->createCommand($insert);
            if ($insertCom->execute()) {
                $msg["code"] = 0;
            }
        }  else {
            $msg['msg'] = "无数据更新";
        }
        echo json_encode($msg);
    }
    
        /**
     * 通过open_id 抓取用户详细信息
     * @return json 是否成功
     */
    public function actionGetfrmwx()
    {
        $msg = $this->msgcode();
        $openid = Yii::app()->request->getParam('openid');
        if(!empty($openid))
        {
            $ret = new Wxcore(Yii::app()->params['weixin']);
            $usr = new User();
            $usrList = $ret->getUsrinfo($openid);
            $val = $usr->findByPk($openid);
            $tpe = $val->type==0?1:$val->type;
            //不关注什么信息都不会有
            if($usrList['subscribe']==0)
            {
                $usr->updateAll(array("subscribe"=>$usrList['subscribe'],"type"=>$tpe),'open_id=:open_id',array(':open_id'=>$openid));
            }else
            {
                $grp = $ret->getUsrgroup($openid);
                $usr->updateAll(array('nickname'=>$usrList['nickname'],'group_id'=>$grp['groupid'],'sex'=>$usrList['sex'],'city'=>$usrList['city'],'province'=>$usrList['province'],
                'country'=>$usrList['country'],"subscribe"=>$usrList['subscribe'],"type"=>$tpe),'open_id=:open_id',array(':open_id'=>$openid));
            }
            $msg['code'] = 0;  
        }
        echo json_encode($msg);
        Yii::app()->end();
    }
    
   /**
    * 手动更新用户信息
    */
    public function actionUpdate()
    {
        $openid = Yii::app()->request->getParam('openid');
        if(!empty($openid))
        {
            $usr = new User();  
            //判断用户微信id是否存在
            $usrInfo = $usr->findByPk($openid);
            //关注的人才可以编辑
            if($usrInfo->subscribe==1)
            {
                $grpList = Group::model()->findAll();
                 $this->renderPartial('update', array(
                'usrInfo' => $usrInfo,
                'grpList'=>$grpList
                ), false, true);
            }
            else
            {
                echo "关注并且验证了邮件的人才可以编辑";
            }   
        }else
        {
            echo "记录不存在";
        }
    }
    /**
     * 保存手动更新后的内容
     */
    public function actionSave()
    {
        $msg = $this->msgcode();
        $open_id = Yii::app()->request->getParam('open_id');
        $name = Yii::app()->request->getParam('name');
        $employee_id = Yii::app()->request->getParam('employee_id');
        $group_id = Yii::app()->request->getParam('group_id');
        $tel = Yii::app()->request->getParam('tel');
        $email = Yii::app()->request->getParam('email');
        if(!empty($open_id))
        {
            $ret = new Wxcore(Yii::app()->params['weixin']);
            $usr = new User();
            $postid = $usr->findByPk($open_id);
            if($postid->subscribe==1)
            {
                if(!empty($group_id)&&$postid['group_id']!=$group_id)
                    $ret->transGroup($open_id,$group_id);
                 $usr->updateAll(array('name'=>$name,'employee_id'=>$employee_id,'email'=>$email,'group_id'=>$group_id,'tel'=>$tel),'open_id=:open_id',array(':open_id'=>$open_id));
                 $msg['code'] = 0;
            }
            else
            {
                $msg['msg'] = "关注并且验证了邮件的人才可以编辑";
            }
        }
        echo json_encode($msg);
        Yii::app()->end();
    }

    /**
     * 删除取消关注的人
     */
    public function actionDel()
    {
        $msg = $this->msgcode();
        $open_id = Yii::app()->request->getParam('openid');
        if(!empty($open_id))
        {
            if(User::model()->deleteByPk($open_id))
            {
                $this->msgsucc($msg);
            }else{
                $msg['code'] = '删除失败';
            }
        }
        echo json_encode($msg);
        Yii::app()->end();
    }


   /**
    * 分组列表
    */
    public function actionGroup() {
        //先获取当前是否有页码信息
        $pages['pageNum'] = Yii::app()->getRequest()->getParam("pageNum", 1); //当前页
        $pages['countPage'] = Yii::app()->getRequest()->getParam("countPage", 0); //总共多少记录
        $pages['orderField'] = Yii::app()->request->getParam("orderField","id"); // 排序字段
        $pages['numPerPage'] = Yii::app()->request->getParam("numPerPage",30); // 每页显示多少条
        $pages['orderDirection'] = Yii::app()->request->getParam("orderDirection","DESC"); // 每页显示多少条
        $pages['orderDirection'] = $pages['orderDirection']!=="asc"?" DESC":" ASC";
        
        //构造SQL
        $criteria = new CDbCriteria;
        $criteria->limit = $pages['numPerPage'];
        $criteria->offset = $pages['numPerPage'] * ($pages['pageNum'] - 1);
        $criteria->order = $pages['orderField'].$pages['orderDirection'];
        if (empty($pages['countPage']))
            $pages['countPage'] = Group::model()->count($criteria);
        $models = Group::model()->findAll($criteria);
        $this->renderPartial('group', array(
            'models' => $models,
            'pages' => $pages,), false, true);
    }
    
    /**
     * 从微信服务器获取分组列表
     * 删除老列表，更新新分组列表
     */
    public function actionUpdateGrp() {
        $msg = $this->msgcode();
        try {
            $ret = new Wxcore(Yii::app()->params['weixin']);
            $group = $ret->getGroup();
            if($group!=FALSE)
            {
                $tbname = Group::model()->tableName();
                $insert = "INSERT INTO ".$tbname." VALUES"; //构造SQL
                foreach ($group['groups'] as $value) {
                    $insert .= sprintf("(%d,'%s'),",$value['id'],$value['name']);
                }
                $insert = rtrim($insert,",");
                $connection = Yii::app()->db;
                $deleCom = $connection->createCommand("TRUNCATE TABLE `".$tbname."`");
                $deleCom->execute();
                $insertCom = $connection->createCommand($insert);
                if ($insertCom->execute()) {
                    $msg["code"] = 0;
                }
            }
        } catch (Exception $exc) {
            $msg['msg'] = $exc;
        }
        echo json_encode($msg);
    }
    
     /**
     * 编辑分组
     */
    public function actionEditGrp() {
        $id = Yii::app()->getRequest()->getParam("id",-1); //是否为修改
        $models = array();
        if($id!=""&&$id!=-1)
        {
            $models = Group::model()->findByPk($id);
        }
        $this->renderPartial('_aeGrp', array('models' => $models), false, true);
    }
    /**
     * 编辑分组
     */
    public function actionGrpSave() {
        $msg = $this->msgcode();
        $id = Yii::app()->getRequest()->getParam("id",-1); 
        $name = Yii::app()->getRequest()->getParam("name","");
        if(empty($name))
        {
            $msg['msg'] = "分组名称不能为空";
        }else
        {
            $ret = new Wxcore(Yii::app()->params['weixin']);
            $Group = new Group();
            if($id!=""&&$id!=-1)
            { 
                if($ret->updateGroup($id,$name))
                {
                    $models = $Group->findByPk($id);
                    $models->name = $name;
                    $models->save();
                    $msg['code'] = 0;
                }
                else
                {
                    $msg['msg'] = "更新失败！";
                } 
            }else
            {
                $tmp = $ret->createGroup($name);
                if($tmp==FALSE)
                {
                     $msg['msg'] = "添加失败！";
                }else
                {
                    $Group->id = $tmp['group']['id'];
                    $Group->name = $tmp['group']['name'];
                    $Group->save();
                    $msg['code'] = 0;
                }
            }
        }
        echo json_encode($msg);
    }
    public function actionRefwx()
    {
        $msg = $this->msgcode();
        Yii::app()->redis->getClient()->del("access_token");
        $msg['code'] = 0;
        echo json_encode($msg);
    }
}
