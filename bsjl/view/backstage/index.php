<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>百胜餐饮集团-成都后台管理</title>
        <link href="<?php echo Application::$_config['home']['url']; ?>/public/css/style.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="<?php echo Application::$_config['home']['url']; ?>/public/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
        <!--[if IE]>
        <link href="themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
        <![endif]-->

        <!--[if lte IE 9]>
        <script src="js/speedup.js" type="text/javascript"></script>
        <![endif]-->

        <script src="<?php echo Application::$_config['home']['url']; ?>/public/js/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo Application::$_config['home']['url']; ?>/public/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="<?php echo Application::$_config['home']['url']; ?>/public/js/dwz.min.js" type="text/javascript"></script>
        <script src="<?php echo Application::$_config['home']['url']; ?>/public/js/dwz.regional.zh.js" type="text/javascript"></script>
        <script src="<?php echo Application::$_config['home']['url']; ?>/public/xheditor/xheditor-zh-cn.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(function() {
                DWZ.init("../dwz.frag.xml", {
        //		loginUrl:"login_dialog.html", loginTitle:"登录",	// 弹出登录对话框
        //		loginUrl:"login.html",	// 跳到登录页面
                    statusCode: {ok: 200, error: 300, timeout: 301}, //【可选】
                    pageInfo: {pageNum: "pageNum", numPerPage: "numPerPage", orderField: "orderField", orderDirection: "orderDirection"}, //【可选】
                    debug: false, // 调试模式 【true|false】
                    callback: function() {
                        initEnv();
                        $("#themeList").theme({themeBase: "themes"}); // themeBase 相对于index页面的主题base路径
                    }
                });
            });

        </script>
    </head>

    <body scroll="no">
        <div id="layout">
            <div id="header">
                <div class="headerNav">
                    <a class="logo" href="<?php echo Application::$_config['home']['url']; ?>">后台</a>
                    <ul class="nav">
                        <li><a href="<?php echo Application::$_config['home']['url']; ?>" target="_blank">首页</a></li>
                        <li><a href="login_index.html">退出</a></li>
                    </ul>
                </div>
            </div>
            <div id="leftside">
                <div id="sidebar_s">
                    <div class="collapse">
                        <div class="toggleCollapse"><div></div></div>
                    </div>
                </div>
                <div id="sidebar">
                    <div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>

                    <div class="accordion" fillSpace="sidebar">
                        <div class="accordionHeader">
                            <h2><span>Folder</span>后台管理</h2>
                        </div>
                        <div class="accordionContent">
                            <ul class="tree treeFolder">
                                <li><a href="#">简历管理</a>
                                    <ul>
                                        <li><a href="<?php echo Application::$_config['home']['url']; ?>/index.php/backstage_registration.html" target="navTab" rel="artpush">简历查看</a></li>
                                        <li><a href="<?php echo Application::$_config['home']['url']; ?>/index.php/backstage_recommend.html" target="navTab" rel="recommend">推荐简历查看</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div id="container">
                <div id="navTab" class="tabsPage">
                    <div class="tabsPageHeader">
                        <div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
                            <ul class="navTab-tab">
                                <li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">我的主页</span></span></a></li>
                            </ul>
                        </div>
                        <div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
                        <div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
                        <div class="tabsMore">more</div>
                    </div>
                    <ul class="tabsMoreList">
                        <li><a href="javascript:;">我的主页</a></li>
                    </ul>
                    <div class="navTab-panel tabsPageContent layoutBox">
                        <div class="page unitBox">
                            <div class="pageFormContent" layoutH="80" style="margin-right:230px">
                                <div class="divider"></div>
                                <h2>后台系统一期说明文档:</h2><br/><br/>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div id="footer">Copyright &copy; 2010 <a href="http://it2048.cn/" target="blank">由小熊开发</a> 川ICP备05019125号-10</div>

    </body>
</html>