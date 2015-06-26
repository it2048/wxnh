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
        $model = new Homeconf();
        $lst = $model->getList();
        $this->renderPartial('index', array(
            'models' => $allList,'lst'=>$lst,
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
        $month = Yii::app()->request->getParam('month',0);
        $brand = Yii::app()->request->getParam('brand',0);
        $dm = Yii::app()->request->getParam('dm','');
        $city = Yii::app()->request->getParam('city','');
        $am_sge = Yii::app()->request->getParam('am_sge','');
        $am_time = Yii::app()->request->getParam('am_time',0);
        $am_add = Yii::app()->request->getParam('am_add','');
        $am_people = Yii::app()->request->getParam('am_people',0);
        $oje_ct = Yii::app()->request->getParam('oje_ct','');
        $oje_time = Yii::app()->request->getParam('oje_time',0);
        $oje_add = Yii::app()->request->getParam('oje_add','');
        $oje_people = Yii::app()->request->getParam('oje_people',0);
        $dm_time = Yii::app()->request->getParam('dm_time',0);
        $dm_add = Yii::app()->request->getParam('dm_add','');
        $dm_people = Yii::app()->request->getParam('dm_people',0);
        $data = array(
            'month' => intval($month),
            'brand' => $brand,
            'dm' => $dm,
            'zmzy' => $this->getUserName(),
            'city' => $city,
            'am_sge' => $am_sge,
            'am_time' => strtotime($am_time),
            'am_add' => $am_add,
            'am_people' => $am_people,
            'oje_ct' => $oje_ct,
            'oje_time' => strtotime($oje_time),
            'oje_add' => $oje_add,
            'oje_people' => $oje_people,
            'dm_time' => strtotime($dm_time),
            'dm_add' => $dm_add,
            'dm_people' => $dm_people,
        );

        $kk = new WxInterview();
        foreach($data as $k=>$val)
        {
            $kk->$k = $val;
        }
        if($kk->save())
        {
            $msg['code'] = 0;
        }
        echo json_encode($msg);
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
