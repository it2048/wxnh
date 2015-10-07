<?php

/**
 * 核心控制器 
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */
class Controller {

    public function __construct() {
        header('Content-type:text/html;chartset=utf-8'); 
    }

    /**
     * 实例化模型 
     * @access      final   protected 
     * @param       string  $model  模型名称 
     */
    final protected function model($model) {
        if (empty($model)) {
            trigger_error('不能实例化空模型');
        }
        require MODEL_PATH .'/'.$model.".php" ;
        return new $model;
    }

    /**
     * 加载系统配置,默认为系统配置 $CONFIG['system'][$config] 
     * @access      final   protected 
     * @param       string  $config 配置名  
     */
    final protected function config($config) {
        return Application::$_config[$config];
    }

    /**
     * 加载模板文件 
     * @access      final   protect 
     * @param       string  $path   模板路径 
     * @return      string  模板字符串 
     */
    final protected function showTemplate($path, $data = array()) {
        $template = new Template();
        $template->init($path, $data);
        $template->outPut();
    }
}
