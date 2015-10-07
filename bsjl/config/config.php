<?php

/**
 * 系统配置文件 
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */
/* 数据库配置 */
$CONFIG['system']['db'] = array(
    'db_host' => '120.25.161.122:33060',
    'db_user' => 'weixin',
    'db_password' => 'abcd1234',
    'db_database' => 'weixin',
);

/* 博客配置 */
$CONFIG['system']['home'] = array(
    'url' => 'http://120.25.161.122/wx/bsjl',
    'urltmp' => '120.25.161.122/wx/bsjl'
);

/* 博客特殊参数 */
$CONFIG['system']['par'] = array(
    '1' => '已发布',
    '2' => '草稿',
    '3' => '不公开',
    '4' => '置顶',
    '5' => '幻灯',
);

/* 后台登录帐号与密码 */
$CONFIG['system']['back'] = array(
    'user' => 'bsdit2048',
  'pwd' => 'it2048bsd',
    'key' => '@#$%%^xifanglei',
    'times' => 3,
    'time' => 1800,
);

/* 自定义类库配置 */
$CONFIG['system']['lib'] = array(
    'prefix' => 'my'   //自定义类库的文件前缀 
);

$CONFIG['system']['route'] = array(
    'default_controller' => 'index', //系统默认控制器 
    'default_action' => 'index', //系统默认控制器 
    'url_type' => 2 /* 定义URL的形式 1 为普通模式    index.php?c=controller&a=action&id=2 
         *              2 为PATHINFO   index.php/controller/action/id/2(暂时不实现)              
         */
);

/* 缓存配置 */
$CONFIG['system']['cache'] = array(
    'cache_dir' => 'cache', //缓存路径，相对于根目录 
    'cache_prefix' => 'cache_', //缓存文件名前缀 
    'cache_time' => 1800, //缓存时间默认1800秒 
    'cache_mode' => 2, //mode 1 为serialize ，model 2为保存为可执行文件    
);
