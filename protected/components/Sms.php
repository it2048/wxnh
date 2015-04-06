<?php
/**
 * Created by PhpStorm.
 * User: xiongfanglei
 * Date: 15-2-11
 * Time: 上午10:06
 */
class Sms {

    public function sendNotice($mobile,$sb="")
    {

        if(!empty($sb))
        {
            $msg = $this->insert($sb);
            if($msg['code']==0)
            {
                return $this->insert($mobile);
            }
            else
            {
                return $msg;
            }
        }else
        {
            return $this->insert($mobile);
        }

    }

    private function insert($sb)
    {
        $msg = array("code"=>1,"msg"=>"");
        $tm = time()-86400;
        $tmj = time()-90;
        $model = SmsNotice::model()->findByPk($sb);
        if(!empty($model))
        {
            if($model->ctn>=10)
            {
                if($tm<$model->ftime)
                    $msg['msg'] = "验证码发送太频繁，请稍后再试。";
                else
                {
                    $model->ftime = time();
                    $model->ctn = 1;
                    $model->ltime = time();
                    $model->save();
                }
            }
            else{
                if($tm>$model->ftime)
                {
                    $model->ftime = time();
                    $model->ctn = 1;
                    $model->ltime = time();
                    $model->save();
                }else{
                    if($model->ltime>$tmj)
                    {
                        $msg['msg'] = "请勿短时间内连续发送短信";
                    }else
                    {
                        $model->ctn += 1;
                        $model->ltime = time();
                        $model->save();
                    }
                }
            }
        }
        else{
            $mod = new SmsNotice();
            $mod->telorsb = $sb;
            $mod->ftime = time();
            $mod->ctn = 1;
            $mod->ltime = time();
            $mod->save();
        }
        if(empty($msg['msg']))
            $msg['code'] = 0;
        return $msg;
    }

    public function sendSMS($mobile,$content,$type="identify")
    {
        $data = array
        (
            'username'=>Yii::app()->params['sms']['username'],					//用户账号
            'pwd'=>Yii::app()->params['sms']['password'],				//密码
            'phones'=>$mobile,					//号码
            'setTime'=>'',
            'contents'=>$content,				//内容
            'scode'=>''
        );
        $result= $this->curlSMS(Yii::app()->params['sms']['url'],$data);			//POST方式提交
        $model = new SmsSend();
        $model->content = $content;
        $model->tel = $mobile;
        $model->time = time();
        $model->type = $type;
        if($result)
        {
            $model->rtn = 0; //成功
            $model->save();
            return true;
        }
        else{
            $model->rtn = 1;
            $model->save();
            return false;
        }
    }
    private function curlSMS($url,$post_fields=array()){
        $tmp = $url."?".http_build_query($post_fields);
        $setll = file_get_contents($tmp);
        $data = simplexml_load_string($setll, 'SimpleXMLElement', LIBXML_NOCDATA);
        if($data[0]==1)
        {
            return true;
        }else{
            return false;
        }
    }

}
