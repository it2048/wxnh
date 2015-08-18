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

        if($data['hr_time']<$data['am_time'])
        {
            $msg['msg'] = 'am时间不能小于hr时间';
        }elseif($data['am_time']<$data['oje_time'])
        {
            $msg['msg'] = 'oje时间不能小于am时间';
        }elseif($data['oje_time']<$data['dm_time'])
        {
            $msg['msg'] = 'dm时间不能小于oje时间';
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

            if($data['hr_time']<$data['am_time'])
            {
                $msg['msg'] = 'am时间不能小于hr时间';
            }elseif($data['am_time']<$data['oje_time'])
            {
                $msg['msg'] = 'oje时间不能小于am时间';
            }elseif($data['oje_time']<$data['dm_time'])
            {
                $msg['msg'] = 'dm时间不能小于oje时间';
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

    public function actionExp()
    {
        $allList = AppBsContracts::model()->findAll();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="劳动合同追踪信息表.csv"');
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        // 输出Excel列名信息
        $arr = TempList::$Contracts;
        array_push($arr,"导入时间");
        array_push($arr,"邮寄时间");
        array_push($arr,"餐厅处理时间");
        array_push($arr,"餐厅处理状态");

        $head = $arr;
        foreach ($head as $i => $v) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$i] = iconv('utf-8', 'gbk', $v);
        }
        // 将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);
        // 计数器
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;

        foreach($allList as $value)
        {
            $cnt ++;
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }

            $dearr = explode("|",$value->desc);

            $row = array(-1=>$value['bm_id'])+$dearr+array(
                    19=>empty($value['dr_time'])?"":date("Y-m-d H:i:s",$value['dr_time']),
                    20=>empty($value['yj_time'])?"":date("Y-m-d H:i:s",$value['yj_time']),
                    21=>empty($value['ct_time'])?"":date("Y-m-d H:i:s",$value['ct_time']),
                    22=>TempList::$ct_status[$value['stage']]
                );
            foreach ($row as $i => $v) {
                // CSV的Excel支持GBK编码，一定要转换，否则乱码
                $row[$i] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($fp, $row);
        }
    }

}
