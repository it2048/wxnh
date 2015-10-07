<?php

/**
 * 模板类 
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */
final class Template {

    public $template_name = null;
    public $data = array();
    public $out_put = null;

    public function init($template_name, $data = array()) {
        $this->template_name = $template_name;
        $this->data = $data;
        $this->fetch();
    }
    /**
     * 加载模板文件 
     * @access      public 
     * @param       string  $file 
     */
    public function fetch() {
        $view_file = VIEW_PATH . '/' . $this->template_name . '.php';
        if (file_exists($view_file)) {
            extract($this->data);
            ob_start();
            require $view_file;
            $content = ob_get_contents();
            ob_end_clean();
            $this->out_put = $content;
        } else {
            trigger_error('加载 ' . $view_file . ' 模板不存在');
        }
    }

    /**
     * 输出模板 
     * @access      public  
     * @return      string 
     */
    public function outPut() {
        if(Extension_Loaded('zlib')) Ob_Start('ob_gzhandler');
        Header("Content-type: text/html");
        echo $this->out_put;
        if(Extension_Loaded('zlib')) Ob_End_Flush();
    }

}
