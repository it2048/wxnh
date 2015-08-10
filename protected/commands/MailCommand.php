<?php
/**
 * Created by PhpStorm.
 * User: xfl
 * Date: 2015/6/13
 * Time: 1:38
 */

class MailCommand extends CConsoleCommand
{

    public function actionIndex()
    {
        $obj= new ReceiveMail('it2048@163.com','lnrxmvauvzdeujjy','it2048@163.com','pop.163.com','pop3','110',false);
        $obj->connect();         //If connection fails give error message and exit
        $tot=$obj->getTotalMails(); //Total Mails in Inbox Return integer value
        $mailH = 'datainsighttool@Kenexa.com';
        for($i=$tot;$i>0;$i--)
        {
            $head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName)
            if(trim($head['from'])==$mailH&&date('Ymd', trim($head['time']))==date('Ymd'))
            {
                $str=$obj->GetAttach($i,""); // Get attached File from Mail Return name of file in comma separated string  args. (mailid, Path to store file)
                preg_match_all ('/<a href=\"(.*?)\".*?>(.*?)<\/a>/i',$str,$matches);
                $url = $matches[1][1];
                echo "下载文件中……\r\n";
                $content = file_get_contents($url);
                echo "存储文件中……\r\n";
                $filename =  Yii::app()->basePath . '/../public/csv/'.date('Ymd').'.csv';
                file_put_contents($filename,$content);
                $em = new WxNewEmployee();
                echo "解析文件中……\r\n";
                echo $em->storeCsv($filename);
                //@unlink($filename);
                break;
            }
        }
        $obj->close_mailbox();   //Close Mail Box
    }

    public function actionWx()
    {
        $ret = new Wxcore(Yii::app()->params['weixin']);
        $usr = new User();
        //以关注的用户更新内容
        $userinfo = $usr->findAll("subscribe=:type",array("type"=>1));

        foreach ($userinfo as $value) {
            $usrList = $ret->getUsrinfo($value->open_id);
            $tpe = $value->type==0?1:$value->type;
            //不关注什么信息都不会有
            if($usrList['subscribe']==0)
            {
                $usr->updateAll(array("subscribe"=>$usrList['subscribe'],"type"=>$tpe),'open_id=:open_id',array(':open_id'=>$value->open_id));
            }else
            {
                $grp = $ret->getUsrgroup($value->open_id);
                $usr->updateAll(array('nickname'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u','',$usrList['nickname']),'group_id'=>$grp['groupid'],'sex'=>$usrList['sex'],'city'=>$usrList['city'],'province'=>$usrList['province'],
                    'country'=>$usrList['country'],"subscribe"=>$usrList['subscribe'],"type"=>$tpe),'open_id=:open_id',array(':open_id'=>$value->open_id));
            }
            $usr->setIsNewRecord(TRUE);
        }
    }


}