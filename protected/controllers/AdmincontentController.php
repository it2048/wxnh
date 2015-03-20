<?php

class AdmincontentController extends AdminSet
{
    /**
     * 生成首页
     *
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect (Yii::app ()->createAbsoluteUrl ('adminlogin/index'));
    }
    
    /**
     * 用户管理
     */
    public function actionUserManager()
    {
        //print_r(Yii::app()->user->getState('username'));
        //先获取当前是否有页码信息
        $pages['pageNum'] = Yii::app()->getRequest()->getParam("pageNum", 1); //当前页
        $pages['countPage'] = Yii::app()->getRequest()->getParam("countPage", 0); //总共多少记录
        $pages['numPerPage'] = Yii::app()->getRequest()->getParam("numPerPage", 50); //每页多少条数据


        $pages['name'] = Yii::app()->getRequest()->getParam("name",""); //按名称查询

        $criteria = new CDbCriteria;
        !empty($pages['name'])&&$criteria->compare('username', $pages['name']);
        $pages['countPage'] = WxAdmin::model()->count($criteria);
        $criteria->limit = $pages['numPerPage'];
        $criteria->offset = $pages['numPerPage'] * ($pages['pageNum'] - 1);
        $allList = WxAdmin::model()->findAll($criteria);
        $this->renderPartial('usermanager', array(
            'models' => $allList,
            'pages' => $pages),false,true);
    }

    public function actionUseradd()
    {
        $this->renderPartial('useradd');
    }
    /**
     * 删除用户
     */
    public function actionUserdelete()
    {
        $msg = $this->msgcode();
        $username = Yii::app()->getRequest()->getParam("username", ""); //用户名
        if($username!="")
        {
            if(WxAdmin::model()->deleteByPk($username))
                $this->msgsucc($msg);
            else
                $msg['msg'] = "数据删除失败";
        }else
        {
            $msg['msg'] = "用户名不能为空";
        }
        echo json_encode($msg);
    }
    /**
     * 编辑用户
     */
    public function actionUseredit()
    {
        $username = Yii::app()->getRequest()->getParam("username", ""); //用户名
        if($username!="")
        {
            $model = WxAdmin::model()->findByPk($username);
            if(!empty($model))
            {
                $this->renderPartial('useredit',array(
                    "models"=>$model
                ));die();
            }     
        }
        $this->renderPartial('useradd');
    }

    
    /**
     * 用户自己修改密码
     */
    public function actionUsernewpass()
    {
        $username = $this->getUserName(); //用户名
        $model = NULL;
        if($username!="")
        {
            $model = WxAdmin::model()->findByPk($username);  
        }
        $this->renderPartial('usernewpass',array(
                    "models"=>$model
                ));
    }
    /**
     * 用户修改密码
     */
    public function actionUsernewsave()
    {
        $msg = $this->msgcode();
        $oldpassword = Yii::app()->getRequest()->getParam("oldpassword", ""); //用户名
        $password = Yii::app()->getRequest()->getParam("password", ""); //用户名
        $tel = Yii::app()->getRequest()->getParam("tel", ""); //用户名
        $email = Yii::app()->getRequest()->getParam("email", ""); //用户名
        $username = $this->getUserName(); //用户名
        if($username!="")
        {
            $model = WxAdmin::model()->findByPk($username);  
            if($password===""||$oldpassword===""||$model->password!=md5($oldpassword)||empty($model))
            {
                $msg['msg'] = "旧密码输入错误";
            }else{
                $model->password = md5($password);
                $model->email = $email;
                $model->tel = $tel;
                if($model->save())
                {
                    $this->msgsucc($msg);
                    $msg['msg'] = "保存成功，请退出后重新登录";
                }else
                {
                    $msg['msg'] = "存入数据库异常";
                }
            }  
        }else
            $msg['msg'] = "您没有权限修改别人的密码";
        echo json_encode($msg);
    }
    
    /**
     * 添加用户
     */
    public function actionUsersave()
    {
        $msg = $this->msgcode();
        $username = Yii::app()->getRequest()->getParam("username", ""); //用户名
        $password = Yii::app()->getRequest()->getParam("password", ""); //用户名
        $tel = Yii::app()->getRequest()->getParam("tel", ""); //用户名
        $email = Yii::app()->getRequest()->getParam("email", ""); //用户名

        if($username===""||$password==="")
        {
            $msg['msg'] = "帐号密码不能为空";
        }else{
            $rsAdmin = new WxAdmin();
            $rsAdmin->username = $username;
            $rsAdmin->password = md5($password);
            $rsAdmin->email = $email;
            $rsAdmin->tel = $tel;
            if($rsAdmin->save())
            {
                $this->msgsucc($msg);
            }else
            {
                $msg['msg'] = "存入数据库异常";
            }
        }
        echo json_encode($msg);
    }
    /**
     * 更新用户
     */
    public function actionUserupdate()
    {
        $msg = $this->msgcode();
        $username = Yii::app()->getRequest()->getParam("username", ""); //用户名
        $password = Yii::app()->getRequest()->getParam("password", ""); //用户名
        $tel = Yii::app()->getRequest()->getParam("tel", ""); //用户名
        $email = Yii::app()->getRequest()->getParam("email", ""); //用户名

        if($username==="")
        {
            $msg['msg'] = "帐号不能为空";
        }else{
            $rsAdmin = WxAdmin::model()->findByPk($username);
            if($password!=="")
                $rsAdmin->password = md5($password);
            $rsAdmin->email = $email;
            $rsAdmin->tel = $tel;
            if($rsAdmin->save())
            {
                $this->msgsucc($msg);
            }else
            {
                $msg['msg'] = "存入数据库异常";
            }
        }
        echo json_encode($msg);
    }
}