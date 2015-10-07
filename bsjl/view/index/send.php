<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>百胜餐饮集团-西南</title>
<meta name="keywords" content="推荐"/>
<meta name="description" content="请提供以下信息以便于我们联系您"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0, maximum-scale=1.0"/>
<link rel="stylesheet" href="<?php echo Application::$_config['home']['url']; ?>/public/style.css" />
<script src="<?php echo Application::$_config['home']['url']; ?>/public/js/jquery.min.js"></script>
</head>
<body>
<div class="g-mn5">
        <div class="m-form">
    <form name="frm" action="<?php echo Application::$_config['home']['url']; ?>/login_addtj.html" method="post">
        <fieldset>
            <legend class="ui-yip">请提供以下信息以便于我们联系您</legend>
            <legend class="ledtmp">伯乐信息：</legend>
            <div class="formitm">
                <label class="lab">姓名：</label>
                <div class="ipt">
                    <input type="text" name="bl_name" class="u-ipt"/>
                    <p>请填写真实信息，方便我们联系到您</p>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">部门/餐厅：</label>
                <div class="ipt">
                    <input type="text"  name="bl_add" class="u-ipt"/>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">手机号码：</label>
                <div class="ipt">
                    <input type="text" name="bl_tel" class="u-ipt"/>
                </div>
            </div>
            <legend class="ledtmp">千里马信息：</legend>
            <div class="formitm">
                <label class="lab">姓名：</label>
                <div class="ipt">
                    <input type="text" name="qlm_name" class="u-ipt"/>
                    <p>请填写真实信息，方便我们联系到您</p>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">手机号码：</label>
                <div class="ipt">
                    <input type="text"  name="qlm_tel" class="u-ipt"/>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">所在城市：</label>
                <div class="ipt">
                    <input type="text"  name="qlm_city" class="u-ipt"/>
                </div>
            </div>
           
            <div class="formitm formitm-1"><button class="u-btn" type="button" onclick="addtags()">提交信息</button></div>
        </fieldset>
    </form>

    </div>
</div>
<script type="text/javascript">
function addtags()
{
    var bl_name = $.trim($("input[name='bl_name']").val());
    var bl_add = $.trim($("input[name='bl_add']").val());
    var bl_tel = $.trim($("input[name='bl_tel']").val());
    var qlm_name = $.trim($("input[name='qlm_name']").val());
    var qlm_tel = $.trim($("input[name='qlm_tel']").val());
    var qlm_city = $.trim($("input[name='qlm_city']").val());
    
    if(bl_name=="") $("input[name='bl_name']").addClass("u-ipt-err");
    else if(bl_add=="") $("input[name='bl_add']").addClass("u-ipt-err");
    else if(qlm_name=="") $("input[name='qlm_name']").addClass("u-ipt-err");
    else if(qlm_city=="") $("input[name='qlm_city']").addClass("u-ipt-err");
    else if(bl_tel=="") $("input[name='bl_tel']").addClass("u-ipt-err");
    else if(qlm_tel=="") $("input[name='qlm_tel']").addClass("u-ipt-err");
    else
    {
        $('form').submit();
    }
	
}
</script>
    <script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Fce94de6e6442ceb7ddbaa4fc195ba2e4' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>
</html>