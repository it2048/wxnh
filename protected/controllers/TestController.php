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
}