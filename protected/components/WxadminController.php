<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  Wx class file.
 *
 * @author �ܷ��� <xiongfanglei@kingsoft.com>
 */
class WxadminController extends CController{
    //通过ajax返回信息
    public $msg = array("code"=>1,"msg"=>"初始化失败","obg"=>NULL);

    //该方法判断用户是否登录 
    public function filterInisession($filterChain){
        $openid = Yii::app()->session->get('openid');
        $time = Yii::app()->session->get('time');
        if(empty($openid)||empty($time)||$time-time()>10800)
        {
            $tp = array(
                "statusCode"=>"301",
                "message"=>"会话超时，请重新登录。",
            );
            echo json_encode($tp);
            Yii::app()->end();
        } 
        $filterChain->run();//参数$filterChain就是执行该filter的action实例，调用$filterChain->run()其实就是执行该action了。
    }
    public function filters(){
        return array('inisession');
    }
}

?>