<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>百胜餐饮集团-西南</title>
<meta name="keywords" content="招聘"/>
<meta name="description" content="请提供以下信息以便于我们联系您"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0, maximum-scale=1.0"/>
<link rel="stylesheet" href="<?php echo Application::$_config['home']['url']; ?>/public/style.css" />
<script src="<?php echo Application::$_config['home']['url']; ?>/public/js/jquery.min.js"></script>
</head>
<body>
<div class="g-mn5">
        <div class="m-form">
    <form name="frm" action="<?php echo Application::$_config['home']['url']; ?>/index.php/login_addtag.html" method="post">
        <fieldset>
            <legend class="ui-yip">请提供以下信息以便于我们联系您</legend>
            <div class="formitm">
                <label class="lab">姓名：</label>
                <div class="ipt">
                    <input type="text" name="name" class="u-ipt"/>
                    <p>请填写真实信息，方便我们联系到您</p>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">应聘品牌：</label>
                <div class="ipt">
                    <select class="u-ipts" name="brand">
                        <option value="肯德基">肯德基</option>
                        <option value="必胜客">必胜客</option>
                        <option value="必胜宅急送">必胜宅急送</option>
                        <option value="东方既白">东方既白</option>
                        <option value="小肥羊">小肥羊</option>
                    </select>
                    <p>选择您最感兴趣的品牌</p>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">所在城市：</label>
                <div class="ipt">
                    <input type="text"  name="ccity" class="u-ipt"/>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">意向城市：</label>
                <div class="ipt">
                    <input type="text"  name="jcity" class="u-ipt"/>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">毕业院校：</label>
                <div class="ipt">
                    <input type="text" name="school" class="u-ipt"/>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">手机号码：</label>
                <div class="ipt">
                    <input type="text" name="tel" class="u-ipt"/>
                </div>
            </div>
            <div class="formitm">
                <label class="lab">电子邮箱：</label>
                <div class="ipt">
                    <input type="text" name="email" class="u-ipt"/>
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
    var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    var name = $.trim($("input[name='name']").val());
    var ccity = $.trim($("input[name='ccity']").val());
    var jcity = $.trim($("input[name='jcity']").val());
    var school = $.trim($("input[name='school']").val());
    var tel = $.trim($("input[name='tel']").val());
    var email = $.trim($("input[name='email']").val());
    if(name=="") $("input[name='name']").addClass("u-ipt-err");
    else if(ccity=="") $("input[name='ccity']").addClass("u-ipt-err");
    else if(school=="") $("input[name='school']").addClass("u-ipt-err");
    else if(tel=="") $("input[name='tel']").addClass("u-ipt-err");
    else if(email=="") $("input[name='email']").addClass("u-ipt-err");
    else if(!myreg.test(email)){
        $("input[name='email']").addClass("u-ipt-err");
    }
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