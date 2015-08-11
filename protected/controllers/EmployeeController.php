<?php
/**
 * Created by PhpStorm.
 * User: xiongfanglei
 * Date: 15-3-21
 * Time: 下午2:19
 */


class EmployeeController extends AdminSet
{
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

        $pages['name'] = Yii::app()->getRequest()->getParam("name",""); //按名称查询
        $pages['stage'] = Yii::app()->getRequest()->getParam("stage",""); //按阶段搜索

        $pages['tel'] = Yii::app()->getRequest()->getParam("tel",""); //按阶段搜索


        $pages['empty_name'] = Yii::app()->getRequest()->getParam("empty_name",""); //按阶段搜索

        $criteria = new CDbCriteria;
        !empty($pages['name'])&&$criteria->compare('employee_name', $pages['name']);
        !empty($pages['stage'])&&$criteria->addSearchCondition('stage',$pages['stage']);
        !empty($pages['empty_name'])&&$criteria->addSearchCondition('empty_name',$pages['empty_name']);

        !empty($pages['tel'])&&$criteria->addSearchCondition('tel',$pages['tel']);


        $pages['countPage'] = WxNewEmployee::model()->count($criteria);

        $criteria->limit = $pages['numPerPage'];
        $criteria->offset = $pages['numPerPage'] * ($pages['pageNum'] - 1);
        $criteria->order = 'id DESC';
        $allList = WxNewEmployee::model()->findAll($criteria);

        $tel = "";
        foreach($allList as $val)
        {
            $tel .= sprintf('"%s",',$val->tel);
        }
        $tel = rtrim($tel,",");
        $hkList = array();
        if(!empty($tel))
        {
            $hook = WxHook::model()->findAll("tel in({$tel})");
            foreach($hook as $value)
            {
                $hkList[$value->tel] = $value->desc;
            }
        }

        $this->renderPartial('index', array(
            'models' => $allList,'hook'=>$hkList,
            'pages' => $pages),false,true);
    }

    /**
     * 导入功能显示页面
     */
    public function actionVimport(){
        $this->renderPartial('_import');
    }

    public function actionHook(){
        $tel = Yii::app()->getRequest()->getParam("tel","");
        $stage = Yii::app()->getRequest()->getParam("stage","");
        $model = WxHook::model()->find("tel=:tl",array(":tl"=>$tel));
        $desc = "";
        $id = "";
        if(!empty($model))
        {
            $id = $model->id;
            $stage = $model->stage;
            $desc = $model->desc;
        }
        $this->renderPartial('_hook',array("id"=>$id,"tel"=>$tel,"stage"=>$stage,"desc"=>$desc));
    }

    public function actionHooksave(){
        $msg = array("code" => 1, "msg" => "失败", "obj" => NULL);
        $tel = Yii::app()->getRequest()->getParam("hktel","");
        $stage = Yii::app()->getRequest()->getParam("hkstage","");
        $desc = Yii::app()->getRequest()->getParam("hkdesc","");
        $id = Yii::app()->getRequest()->getParam("hkid","");

        if(empty($tel)||empty($stage))
        {
            $msg['msg'] = "电话和阶段不能为空";
        }elseif(empty($desc)&&!empty($id))
        {
            WxHook::model()->deleteByPk($id);
            $msg['code'] = 0;
        }else
        {
            if(empty($id))
            {
                $wk = new WxHook();
                $wk->tel = $tel;
                $wk->stage = $stage;
                $wk->desc = $desc;
                if($wk->save())
                {
                    $msg['code'] = 0;
                }else
                {
                    $msg['msg'] = "提交失败";
                }
            }else
            {
                $wk = WxHook::model()->findByPk($id);
                $wk->tel = $tel;
                $wk->stage = $stage;
                $wk->desc = $desc;
                if($wk->save())
                {
                    $msg['code'] = 0;
                }else
                {
                    $msg['msg'] = "提交失败";
                }
            }
        }
        echo json_encode($msg);
    }
    /**
     * 导入功能
     */
    public function actionImport(){
        $msg = array("code" => 1, "msg" => "上传失败", "obj" => NULL);
        if(!empty($_FILES['obj']['name']))
        {
            $_tmp_pathinfo = pathinfo($_FILES['obj']['name']);


            if (strtolower($_tmp_pathinfo['extension'])=="csv") {
                //设置文件路径
                $flname = "upload/".time().".".strtolower($_tmp_pathinfo['extension']);
                $dest_file_path = Yii::app()->basePath . '/../public/'.$flname;
                $filepathh = dirname($dest_file_path);
                if (!file_exists($filepathh))
                    $b_mkdir = mkdir($filepathh, 0777, true);
                else
                    $b_mkdir = true;
                if ($b_mkdir && is_dir($filepathh)) {
                    //转存文件到 $dest_file_path路径
                    if (move_uploaded_file($_FILES['obj']['tmp_name'], $dest_file_path)) {
                        $msg["msg"] = WxNewEmployee::model()->storeCsv($dest_file_path);
                        $msg["code"] = 0;
                        unlink($dest_file_path);
                    } else {
                        $msg["msg"] = '文件上传失败';
                    }
                }
            } else {
                $msg["msg"] = '上传的文件格式需要是csv';
            }
        }
        echo json_encode($msg);
    }

    public function actionDel()
    {
        $msg = $this->msgcode();
        $open_id = Yii::app()->request->getParam('id');
        if(!empty($open_id))
        {
            if(WxNewEmployee::model()->deleteByPk($open_id))
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