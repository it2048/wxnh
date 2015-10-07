<?php 
/** 
 * 应用入口文件 
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */ 
ini_set('date.timezone','Asia/Shanghai');
//禁用错误报告
error_reporting(0);
define('PUBLIC_PATH', dirname(__FILE__));
require PUBLIC_PATH.'/system/app.php'; 
require PUBLIC_PATH.'/config/config.php';
Application::run($CONFIG);
