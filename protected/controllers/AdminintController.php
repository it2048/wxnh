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

        $tel = "";
        foreach($allList as $val)
        {
            $tel .= sprintf('"%s",',$val->zmzy);
        }
        $tel = rtrim($tel,",");
        $hkList = array();
        if(!empty($tel))
        {
            $hook = WxAdmin::model()->findAll("username in({$tel})");
            foreach($hook as $value)
            {
                $hkList[$value->username] = $value->name;
            }
        }

        $model = new Homeconf();
        $lst = $model->getList();
        $this->renderPartial('index', array(
            'models' => $allList,'lst'=>$lst,'userList'=>$hkList,
            'pages' => $pages),false,true);
    }


    public function actionAdd()
    {
        $model = new Homeconf();
        $lst = $model->getList();

        $this->renderPartial('add',array('lst'=>$lst));
    }

    public function actionEdit()
    {
        $id = Yii::app()->request->getParam('id');

        $mod = WxInterview::model()->findByPk($id);
        $model = new Homeconf();
        $lst = $model->getList();
        $this->renderPartial('edit',array('lst'=>$lst,"model"=>$mod));
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
        $hr_time = Yii::app()->request->getParam('hr_time',0);

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
            'hr_time'=>strtotime($hr_time),
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

        if($data['hr_time']>$data['am_time']-172800)
        {
            $msg['msg'] = 'am时间与hr时间间隔不能低于两天';
        }elseif($data['am_time']>$data['oje_time']-172800)
        {
            $msg['msg'] = 'oje时间与am时间间隔不能低于两天';
        }elseif($data['oje_time']>$data['dm_time']-172800)
        {
            $msg['msg'] = 'dm时间与oje时间间隔不能低于两天';
        }else
        {
            $kk = new WxInterview();
            foreach($data as $k=>$val)
            {
                $kk->$k = $val;
            }
            if($kk->save())
            {
                $msg['code'] = 0;
            }
        }

        echo json_encode($msg);
    }


    public function actionUpdate()
    {
        $msg = $this->msgcode();
        $id = Yii::app()->request->getParam('id',0);
        $kk = WxInterview::model()->findByPk($id);
        if(empty($kk))
        {
            $msg['msg'] = "id 不存在";
        }else
        {
            $month = Yii::app()->request->getParam('month',0);
            $brand = Yii::app()->request->getParam('brand',0);
            $dm = Yii::app()->request->getParam('dm','');
            $city = Yii::app()->request->getParam('city','');
            $hr_time = Yii::app()->request->getParam('hr_time',0);
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
                'hr_time'=>strtotime($hr_time),
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

            if($data['hr_time']>$data['am_time']-172800)
            {
                $msg['msg'] = 'am时间与hr时间间隔不能低于两天';
            }elseif($data['am_time']>$data['oje_time']-172800)
            {
                $msg['msg'] = 'oje时间与am时间间隔不能低于两天';
            }elseif($data['oje_time']>$data['dm_time']-172800)
            {
                $msg['msg'] = 'dm时间与oje时间间隔不能低于两天';
            }else
            {
                foreach($data as $k=>$val)
                {
                    $kk->$k = $val;
                }
                if($kk->save())
                {
                    $msg['code'] = 0;
                }else{
                    $msg['msg'] = '数据存储时发生错误';

                }
            }
        }
        echo json_encode($msg);
    }

    /**
     * 删除取消关注的人
     */
    public function actionDel()
    {
        $msg = $this->msgcode();
        $open_id = Yii::app()->request->getParam('id');
        if(!empty($open_id))
        {
            if(WxInterview::model()->deleteByPk($open_id))
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
