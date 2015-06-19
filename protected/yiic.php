<?php
error_reporting(E_ERROR);
$yiic=dirname(__FILE__).'/../../yii/framework/yiic.php';
$config = array_merge(
        require(dirname(__FILE__).'/config/console.php')
        );
require_once($yiic);
