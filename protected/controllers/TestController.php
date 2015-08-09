<?php
/**
 * Created by PhpStorm.
 * User: xfl
 * Date: 2015/6/19
 * Time: 16:17
 */

class TestController extends CController{

    public function actionIndex()
    {
        $obj= new ReceiveMail('it2048@163.com','lnrxmvauvzdeujjy','it2048@163.com','pop.163.com','pop3','110',false);

        $obj->connect();         //If connection fails give error message and exit

        $tot=$obj->getTotalMails(); //Total Mails in Inbox Return integer value

        echo "Total Mails:: $tot<br>";
        $obj->close_mailbox();   //Close Mail Box
    }

    public function actionT()
    {
        $obj= new ReceiveMail('it2048@163.com','lnrxmvauvzdeujjy','it2048@163.com','pop.163.com','pop3','110',false);
        $obj->connect();         //If connection fails give error message and exit
        $tot=$obj->getTotalMails(); //Total Mails in Inbox Return integer value
        $mailH = 'datainsighttool@brassring.com';
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
}