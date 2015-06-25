<?php

class AdminintController extends AdminSet
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='';



    /**
     * 生成首页
     *
     */
    public function actionIndex()
    {
        //print_r(Yii::app()->user->getState('username'));
        //先获取当前是否有页码信息
        $pages['pageNum'] = Yii::app()->getRequest()->getParam("pageNum", 1); //当前页
        $pages['countPage'] = Yii::app()->getRequest()->getParam("countPage", 0); //总共多少记录
        $pages['numPerPage'] = Yii::app()->getRequest()->getParam("numPerPage", 50); //每页多少条数据

        $pages['month'] = Yii::app()->getRequest()->getParam("month",""); //按名称查询

        $criteria = new CDbCriteria;
        !empty($pages['month'])&&$criteria->compare('month', $pages['month']);
        $pages['countPage'] = WxInterview::model()->count($criteria);

        $criteria->limit = $pages['numPerPage'];
        $criteria->offset = $pages['numPerPage'] * ($pages['pageNum'] - 1);
        $criteria->order = 'id DESC';
        $allList = WxInterview::model()->findAll($criteria);

        $this->renderPartial('index', array(
            'models' => $allList,
            'pages' => $pages),false,true);
    }

    /**
     * 手动更新用户信息
     */
    public function actionEdit()
    {
        $id = Yii::app()->request->getParam('id');
        if(!empty($id))
        {
            $usrInfo = WxInterview::model()->findByPk($id);
            //关注的人才可以编辑
            $grpList = Group::model()->findAll();
            $this->renderPartial('edit', array(
                'usrInfo' => $usrInfo
            ), false, true);
        }else
        {
            echo "记录不存在";
        }
    }

    public function actionAdd()
    {
        $model = new Homeconf();
        $lst = $model->getList();

        $this->renderPartial('add',array('lst'=>$lst));
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

}
