<?php

/**
 * URL处理类 
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */
final class Route {

    public $url_query;
    public $url_type;
    public $route_url = array();

    public function __construct() {
        $this->url_query = empty($_SERVER['REQUEST_URI'])?"":$_SERVER['REQUEST_URI'];
    }

    /**
     * 设置URL类型 
     * @access public 
     */
    public function setUrlType($url_type = 2) {
        if ($url_type > 0 && $url_type < 3) {
            $this->url_type = $url_type;
        } else {
            trigger_error("指定的URL模式不存在！");
        }
    }

    /**
     * 获取数组形式的URL  
     * @access      public 
     */
    public function getUrlArray() {
        $this->makeUrl();
        return $this->route_url;
    }

    /**
     * @access      public 
     */
    public function makeUrl() {
        switch ($this->url_type) {
            case 1:
                $this->querytToArray();
                break;
            case 2:
                $this->pathinfoToArray();
                break;
        }
    }

    /**
     * 将query形式的URL转化成数组 
     * @access      public 
     */
    public function querytToArray() {
        $arr = !empty($this->url_query['query']) ? explode('&', $this->url_query['query']) : array();
        $array = $tmp = array();
        if (count($arr) > 0) {
            foreach ($arr as $item) {
                $tmp = explode('=', $item);
                $array[$tmp[0]] = $tmp[1];
            }
            if (isset($array['app'])) {
                $this->route_url['app'] = $array['app'];
                unset($array['app']);
            }
            if (isset($array['controller'])) {
                $this->route_url['controller'] = $array['controller'];
                unset($array['controller']);
            }
            if (isset($array['action'])) {
                $this->route_url['action'] = $array['action'].'Action';
                unset($array['action']);
            }
            if (count($array) > 0) {
                $this->route_url['params'] = $array;
            }
        } else {
            $this->route_url = array();
        }
    }

    /**
     * 将PATH_INFO的URL形式转化为数组 
     * @access      public 
     */
    public function pathinfoToArray() {
        $url = strrchr($this->url_query,'/');
        if(!$url||strpos($this->url_query,"public")!==FALSE)$url ="";
        $stri = strpos($url,".html");
        if($stri!==false)
        {
            $url = str_replace('/', '',$url);
            $url = substr($url, 0,$stri-1);
            $arr = !empty($this->url_query) ? explode('_',$url) : array();
            if(count($arr)>=2)
            {
                $aci = array("index", "recommend", "send", "tag","getAjax");
                if(in_array($arr[0],$aci))
                {
                    $this->route_url['controller'] = "index";
                    if (isset($arr[0])) {
                        $this->route_url['action'] = $arr[0].'Action';
                    }
                    if(isset($arr[1]))
                    {
                         $this->route_url['params'] = array_slice($arr,1);
                    }
                }
                else
                {
                    if (isset($arr[0])) {
                        $this->route_url['controller'] = $arr[0];
                    }
                    if (isset($arr[1])) {
                        $this->route_url['action'] = $arr[1].'Action';
                    }
                    if(isset($arr[2]))
                    {
                         $this->route_url['params'] = array_slice($arr,2);
                    } 
                } 
                unset($arr);
            }else {
                header('HTTP/1.1 404 Not Found');
                header("status: 404 Not Found");
                include '404.php';die();
            }
        }else if(trim($url)==""||$url=="/")
        {
            $this->route_url = array(
                    "controller"=>"index",
                    "action"=>"indexAction"
                );
        }else
        {
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            include '404.php';die();
        }
    }

}
