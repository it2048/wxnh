<?php

/**
 * 核心模型类 
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */
class Model {

    protected $db = null;
    protected static $conn = null;
    public static $instances;

    public function __construct() {
        header('Content-type:text/html;chartset=utf-8');
        if(empty(self::$conn))
        {
            $config_db = $this->config('db');
            self::$conn = new ezSQL_mysql($config_db['db_user'], $config_db['db_password'], $config_db['db_database'],$config_db['db_host']);
            self::$conn->query("set names utf8");
        }
        $this->db = self::$conn;
    }
    /**
     * 加载系统配置,默认为系统配置 $CONFIG['system'][$config] 
     * @access      final   protected 
     * @param       string  $config 配置名  
     */
    final protected function config($config = '') {
        return Application::$_config[$config];
    }

}
