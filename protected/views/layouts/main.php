<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微信公众号管理平台</title>

<link href="<?php $baseUrl =  Yii::app()->baseUrl."/public/"; echo $baseUrl; ?>default/style.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="<?php echo $baseUrl; ?>css/core.css" rel="stylesheet" type="text/css" media="screen"/>

<!--[if IE]>
<link href="<?php echo $baseUrl; ?>/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
<![endif]-->
<script src="<?php echo $baseUrl; ?>js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="<?php echo $baseUrl; ?>js/dwz.min.js" type="text/javascript"></script>
<script src="<?php echo $baseUrl; ?>js/jquery.validate.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(function(){
	DWZ.init("<?php echo $baseUrl; ?>dwz.frag.xml", {
		loginUrl:"<?php echo Yii::app()->createAbsoluteUrl('login/index'); ?>",	// 跳到登录页面
           statusCode:{ok:200, error:300, timeout:301}, //【可选】
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"themes"}); // themeBase 相对于index页面的主题base路径
		}
	});
});
</script>
</head>

<body scroll="no">
	<div id="layout">
		<div id="header">
            <div class="headerNav">
                <ul class="nav">
                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('admincontent/usernewpass'); ?>" target="dialog" width="600">设置</a></li>
                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('adminlogin/index'); ?>" target="_blank">首页</a></li>
                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('admincontent/logout'); ?>">退出</a></li>
                </ul>
            </div>

			<!-- navMenu -->
			
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
						<h2><span>Folder</span>界面组件</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a>用户管理</a>
								<ul>
                                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('admincontent/usermanager'); ?>" target="navTab" rel="usermaneger">管理员管理</a></li>
                                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('employee/index'); ?>" target="navTab" rel="employee">员工管理</a></li>
                                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('user/index'); ?>" target="navTab" rel="userlist">用户列表</a></li>
                                    <li><a href="<?php echo Yii::app()->createAbsoluteUrl('user/group'); ?>" target="navTab" rel="grouplist">分组列表</a></li>
								</ul>
							</li>
                            <li><a>互动平台</a>
								<ul>
									<li><a href="<?php echo Yii::app()->createAbsoluteUrl('sendmsg/index'); ?>" target="navTab" rel="msglist">信息列表</a></li>
								</ul>
							</li>
                            <li><a>系统设置</a>
								<ul>
									<li><a href="<?php echo Yii::app()->createAbsoluteUrl('menu/index'); ?>" target="navTab" rel="menuset">微信菜单设置</a></li>
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
                            <div class="accountInfo">
                                <h1>
                                      Version 0.1
                                </h1>
                            </div>
                            <div class="pageFormContent" layoutH="80" style="margin-left: 230px;">
                                微信公众号管理平台<br /><br />
                                <ul>
                                    <li>1、用户管理；</li>
                                    <li>ps、由于微信接口有次数限制，分组管理只能在微信管理页面进行，地址: <a href="https://mp.weixin.qq.com" target="_blank"> https://mp.weixin.qq.com</a>；</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

	</div>

	<div id="footer">Copyright &copy; xfl.</div>
</body>
</html>