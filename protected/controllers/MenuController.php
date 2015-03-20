<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  Menu class file.
 *
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class MenuController extends AdminSet{
    /**
	 * 显示主页面
	 */
	public function actionIndex()
	{
            $lst = Menu::model()->findAll();
            $models = array();
            foreach ($lst as $value) {
                if(empty($value['parent']))
                {
                    if($value['type']==0)
                        $models[$value['name']]['name'] = $value['name'];
                    else
                    {
                        $models[$value['name']]['type'] = $value['type']==1?"view":"click";
                        $models[$value['name']]['name'] = $value['name'];
                        $mp = $value['type']==1?"url":"key";
                        $models[$value['name']][$mp] = $value['obj'];
                    }
                }else
                {
                    $models[$value['parent']]['sub_button'][$value['name']]['type'] = $value['type']==1?"view":"click";
                    $models[$value['parent']]['sub_button'][$value['name']]['name'] = $value['name'];
                    $mp = $value['type']==1?"url":"key";
                    $models[$value['parent']]['sub_button'][$value['name']][$mp] = $value['obj'];
                }
            }
            $this->renderPartial('index',array("models"=>$models));
	}
     /**
	 * 获取菜单列表
	 */
	public function actionGet()
	{
            $msg = $this->msgcode();
            $ret = new Wxcore(Yii::app()->params['weixin']);
            $bl = $ret->getMenu();
            if($bl)
            { 
                $bl = json_decode($bl,true);
                $insert = "INSERT INTO ".Menu::model()->tableName()." VALUES"; //构造SQL
                $isttmp = "";
                foreach ($bl['menu']['button'] as  $value) {
                    if(count($value['sub_button'])>0)
                    {
                        $isttmp .= sprintf("('%s',%d,'%s','%s'),",$value['name'],0,"","");
                        foreach ($value['sub_button'] as $val) {
                            $val['parent'] = $value['name'];
                            $isttmp .= $this->getSql($val);
                        }
                    }  else {
                        $value['parent'] = "";
                        $isttmp .= $this->getSql($value);
                    }  
                }
                if($isttmp!="")
                {
                    $insert .= $isttmp;
                    $insert = rtrim($insert,",");
                    $connection = Yii::app()->db;
                    $deleCom = $connection->createCommand("TRUNCATE TABLE `".Menu::model()->tableName()."`");
                    $deleCom->execute();
                    $insertCom = $connection->createCommand($insert);
                    if ($insertCom->execute()) {
                        $msg["code"] = 0;
                    }
                }
            }
            echo json_encode($msg);
	}
    //获取SQl语句
    private function getSql($value)
    {
        if($value['type']=="view")
        {
            $it = 1;
            $oj = "url";
        }else
        {
            $it = 2;
            $oj = "key";
        }
        return sprintf("('%s',%d,'%s','%s'),",$value['name'],$it,$value[$oj],$value['parent']);
    }
        
    /**
	 * 设置微信菜单
	 */
	public function actionSet()
	{
            $msg = $this->msgcode();
            $lsttp = Menu::model()->findAll();
            $models = array();
            //格式化成微信对应的格式
            foreach ($lsttp as $value) {
                if(empty($value['parent']))
                {
                    if($value['type']==0)
                        $models[$value['name']]['name'] =urlencode($value['name']);
                    else
                    {
                        $models[$value['name']]['type'] = $value['type']==1?"view":"click";
                        $models[$value['name']]['name'] = urlencode($value['name']);
                        $mp = $value['type']==1?"url":"key";
                        $models[$value['name']][$mp] = urlencode($value['obj']);
                    }
                }else
                {
                    $models[$value['parent']]['sub_button'][$value['name']]['type'] = $value['type']==1?"view":"click";
                    $models[$value['parent']]['sub_button'][$value['name']]['name'] = urlencode($value['name']);
                    $mp = $value['type']==1?"url":"key";
                    $models[$value['parent']]['sub_button'][$value['name']][$mp] = urlencode($value['obj']);
                }
            }
            $lst = array();
            foreach ($models as $value) {
                if(empty($value['sub_button']))
                    array_push($lst,$value);
                else
                {
                    $lst1['name'] = $value['name'];
                    $lst1['sub_button'] = array();
                    foreach ($value['sub_button'] as $val) {
                        array_push($lst1['sub_button'],$val);
                    }
                    array_push($lst,$lst1);
                }
            }
            $tmp['button'] = $lst;
            $ret = new Wxcore(Yii::app()->params['weixin']);
            $bl = $ret->createMenu(urldecode(json_encode($tmp)));
            if($bl)
            { 
                $msg["code"] = 0;
            }
            echo json_encode($msg);
	}
            
    /**
	 * 更新或者添加菜单
	 */
	public function actionUpdate()
	{
            $name = Yii::app()->request->getParam('name',"");
            $models = array();
            if(!empty($name))
                $models = Menu::model()->findByPk($name);
            $parent = Menu::model()->findAll("parent=:parent",array(":parent"=>""));
                
            $this->renderPartial('_aeMenu',array("models"=>$models,"parent"=>$parent));
	}
    
    /**
	 * 更新或者添加菜单
	 */
	public function actionMenuSave()
	{
            $msg = $this->msgcode();
            $name = Yii::app()->request->getParam('name',"");
            $obj = Yii::app()->request->getParam('obj',"");
            $type = Yii::app()->request->getParam('type',"");
            $parent = Yii::app()->request->getParam('parent',"");
            $nametp = Yii::app()->request->getParam('nametp',"");
            
            if($name=="")
            {
                $msg['msg'] = "标题不能为空！";
            }else if(empty ($parent)&&empty ($obj)&&$type!=0)
            {
                $msg['msg'] = "父标题不为空时，数据与类型不能为空";
            }else
            {
                //修改
                if(!empty($nametp))
                {
                    $menu = new Menu();
                    $models = $menu->findByPk($nametp);
                    if($models->name!="")
                    {
                        $models->name = $name;
                        $models->type = $type;
                        $models->obj = $obj;
                        $models->parent = $parent=="0"?"":$parent;
                        $models->save();
                        
                        if(!empty ($parent))
                        {
                            $model = $menu->findByPk($parent);
                            $model->type = 0;
                            $model->obj = "";
                            $model->parent = "";
                            $model->save();
                        }
                        $msg['code'] = 0;
                        $msg['msg'] = '更新成功';
                    }
                }else
                {
                    $models = new Menu();
                    $models->name = $name;
                    $models->type = $type;
                    $models->obj = $obj;
                    $models->parent = $parent==0?"":$parent;
                    $models->save();
                    if(!empty ($parent))
                    {
                        $model = $models->findByPk($parent);
                        $model->type = 0;
                        $model->obj = "";
                        $model->parent = "";
                        $model->save();
                    }
                    $msg['code'] = 0;
                    $msg['msg'] = '添加成功';
                 }
            }
            
            echo json_encode($msg);
	}
    /**
     * 删除
     */
    public function actionDel()
    {
        $msg = $this->msgcode();
        $name = Yii::app()->request->getParam('name',"");
        $name = urldecode($name);
         if(Menu::model()->deleteAll("name=:name or parent=:nm",array(":name"=>$name,":nm"=>$name))>0)
         {
             $msg['code'] = 0;
         }
         echo json_encode($msg);
    }
}

?>