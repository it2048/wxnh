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

        $criteria = new CDbCriteria;
        !empty($pages['name'])&&$criteria->compare('emp_name', $pages['name']);
        $pages['countPage'] = WxEmployee::model()->count($criteria);

        $and = empty($pages['name'])?"":" AND emp_name='{$pages['name']}'";
        $connection = Yii::app()->db;
        $sql = sprintf("SELECT a.*,b.subscribe,b.type FROM wx_employee a left join wx_user b on a.emp_id = b.employee_id WHERE 1 %s limit %d,%d",
            $and,$pages['numPerPage'] * ($pages['pageNum'] - 1),$pages['numPerPage']); //构造SQL

        $allList = $connection->createCommand($sql)->queryAll();

        $this->renderPartial('index', array(
            'models' => $allList,
            'pages' => $pages),false,true);
    }

    /**
     * 导入功能显示页面
     */
    public function actionVimport(){
        $this->renderPartial('_import');
    }
    /**
     * 导入功能
     */
    public function actionImport(){
        $msg = array("code" => 1, "msg" => "上传失败", "obj" => NULL);
        if(!empty($_FILES['obj']['name']))
        {
            $_tmp_pathinfo = pathinfo($_FILES['obj']['name']);


            if ($_tmp_pathinfo['extension']=="csv") {
                //设置文件路径
                $flname = "upload/".time().".".$_tmp_pathinfo['extension'];
                $dest_file_path = Yii::app()->basePath . '/../public/'.$flname;
                $filepathh = dirname($dest_file_path);
                if (!file_exists($filepathh))
                    $b_mkdir = mkdir($filepathh, 0777, true);
                else
                    $b_mkdir = true;
                if ($b_mkdir && is_dir($filepathh)) {
                    //转存文件到 $dest_file_path路径
                    if (move_uploaded_file($_FILES['obj']['tmp_name'], $dest_file_path)) {
                        $msg["msg"] = WxEmployee::model()->storeCsv($dest_file_path);
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
        $open_id = Yii::app()->request->getParam('emp_id');
        if(!empty($open_id))
        {
            if(WxEmployee::model()->deleteByPk($open_id))
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