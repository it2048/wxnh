<?php

/**
 * 应用驱动类 
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */
define('SYSTEM_PATH', PUBLIC_PATH.'/system');
define('ROOT_PATH', substr(SYSTEM_PATH, 0, -7));
define('APP_LIB_PATH', ROOT_PATH . '/lib');
define('SYS_CORE_PATH', SYSTEM_PATH . '/core');
define('CONTROLLER_PATH', ROOT_PATH . '/controller');
define('MODEL_PATH', ROOT_PATH . '/model');
define('VIEW_PATH', ROOT_PATH . '/view');
define('LOG_PATH', ROOT_PATH . '/error/');
final class Application {

    public static $_lib = null;  //库
    public static $_config = null; //配置文件

    public static function init() {
        self::setAutoLibs();
        require SYSTEM_PATH . '/ezsql/ezsql_core.php';
        require SYSTEM_PATH . '/ezsql/ezsql_mysql.php';
        require SYS_CORE_PATH . '/model.php';
        require SYS_CORE_PATH . '/controller.php';
        require SYSTEM_PATH . '/pt.php';
    }

    /**
     * 创建应用 
     * @access      public 
     * @param       array   $config 
     */
    public static function run($config) {
        self::$_config = $config['system'];
        self::init();  //加载文件
        self::autoload(); //实例化类
        self::$_lib['route']->setUrlType(self::$_config['route']['url_type']); 
        $url_array = self::$_lib['route']->getUrlArray();
        self::routeToCm($url_array);
    }

    /**
     * 自动加载类库 
     * @access      public 
     * @param       array   $_lib 
     */
    public static function autoload() {
        foreach (self::$_lib as $key => $value) {
            require (self::$_lib[$key]);
            $lib = ucfirst($key);
            self::$_lib[$key] = new $lib;
        }
        //初始化cache 
        if (!empty(self::$_lib['cache'])&&is_object(self::$_lib['cache'])) {
            self::$_lib['cache']->init(
                    ROOT_PATH . '/' . self::$_config['cache']['cache_dir'], self::$_config['cache']['cache_prefix'], self::$_config['cache']['cache_time'], self::$_config['cache']['cache_mode']
            );
        }
    }

    /**
     * 加载类库 
     * @access      public  
     * @param       string  $class_name 类库名称 
     * @return      object 
     */
    public static function newLib($class_name) {
        $path_arr = explode('_',$class_name);
        $sys_lib = SYSTEM_PATH . '/'. $path_arr[0] . '/.'.$class_name.'.php';
        if (file_exists($sys_lib)) {
            require ($sys_lib);
        }else {
            trigger_error('加载 ' . $class_name . ' 类库不存在');
        }
    }

    /**
     * 自动加载的类库 
     * @access      public 
     */
    public static function setAutoLibs() {
        self::$_lib = array(
            'route' => APP_LIB_PATH . '/lib_route.php',
//            'mysql' => SYS_LIB_PATH . '/lib_mysql.php',
            'template' => APP_LIB_PATH . '/lib_template.php',
//            'cache' => SYS_LIB_PATH . '/lib_cache.php',
//            'thumbnail' => SYS_LIB_PATH . '/lib_thumbnail.php'
        );
    }

    /**
     * 根据URL分发到Controller和Model 
     * @access      public 
     * @param       array   $url_array     
     */
    public static function routeToCm($url_array = array()) {

        $controller = '';
        $action = '';
        $params = '';
        if (isset($url_array['controller'])) {
            $controller = $url_array['controller'];
            $controller_file = CONTROLLER_PATH . '/' . $controller . 'Controller.php';
            
        } else {
            $controller  = self::$_config['route']['default_controller'];
            $controller_file = CONTROLLER_PATH . '/' . self::$_config['route']['default_controller'] . 'Controller.php';
        }
        if (isset($url_array['action'])) {
            $action = $url_array['action'];
        } else {
            $action = self::$_config['route']['default_action'];
        }
        if (isset($url_array['params'])) {
            $params = $url_array['params'];
        }
        if (file_exists($controller_file)) {
            require $controller_file;
            $controller = $controller . 'Controller';
            $controller = new $controller;
            if ($action) {
                if (method_exists($controller, $action)) {
                    isset($params) ? $controller->$action($params) : $controller->$action();
                } else {
                    Application::showError("控制器中方法不存在");
                }
            } else {
                Application::showError('路径的方法为空');
            }
        } else {
            Application::showError('控制器不存在');
        }
    }
    public static function showError($msg)
    {
//        $template = new Template();
//        $template->init("errorpage", array("errormsg"=>$msg));
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            include '404.php';die();
//        $template->outPut();
    }

}
