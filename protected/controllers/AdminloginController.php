<?php

class AdminloginController extends Controller
{
    /**
     * 登录验证
     */
    public function actionLogin()
    {
        $msg = $this->msgcode();
        $username = Yii::app()->request->getParam("username",""); //帐号
        $password = Yii::app()->request->getParam("password",""); //密码
        if($username==""||$password=="")
        {
            $msg['msg'] = "帐号密码不能为空";
        }else
        {
            $_identity = new UserIdentity($username, $password);
            $check_code = $_identity->authenticate();
            if($check_code==0)
            {
                if (Yii::app()->user->login($_identity, 0))
                {
                    Yii::app()->user->setState('username',$_identity->getUserName());
                    Yii::app()->user->setState('time',time());
                    $this->msgsucc($msg);
                }
            }else
            {
                $msg['msg'] = "验证失败";
            }
        }
        echo json_encode($msg);
    }

    public function actionDemo()
    {
        $msg = $this->msgcode();
        $tel = '18780588758';
        $content = '测试短信发送延时时长，测试号码 18228041350';
        $con = new Sms();

        if($con->sendSMS($tel,$content))
            $this->msgsucc($msg);
        else
            $msg['msg'] = '发送短信出错';
        echo json_encode($msg);

    }

    /**
     * 生成首页
     * 
     */
    public function actionIndex()
    {
         $this->renderPartial('loginpage');
    }
}