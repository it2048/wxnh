<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>用户登录</title><meta charset="UTF-8" />
        <link rel="stylesheet" href="<?php echo Application::$_config['home']['url']; ?>/public/css/bootstrap.min.css" />

        <script type="text/javascript" src="<?php echo Application::$_config['home']['url']; ?>/public/js/jquery.min.js"></script>
    </head>
    <body>
    <script type="text/javascript">
    function checkunamenull() {
        var wav_name = $.trim($("#username").val());
        var vav_key = $.trim($("#userkey").val());
        if(wav_name=="")
        {
            $("#unamealert").html("请输入帐号");
            return false;
        }
        if(vav_key=="")
        {
            $("#ukeyalert").html("请输入帐号");
            return false;
        }
        $.get("<?php echo Application::$_config['home']['url']; ?>/login_upld_"+wav_name+"_"+vav_key+".html",function(data){
            var rtmsg = data.split(":");
            if(rtmsg[0]=="0")
            {
                window.location.href = rtmsg[1];
            }
            else
            {
                $("#unamealert").html("登录失败,点击重试");
            }
        });
        return true;
    }
    </script>
        <div class="container projects">
            <div  class="projects-header offset2">
<form class="form-horizontal" action="#">
   <fieldset>
	<legend>登录窗口</legend>
	<div class="control-group">
	  <label class="control-label" for="username">你的ID</label>
	  <div class="controls">
	    <input type="text" class="input-xlarge span2" id="username"> <span class="alert-danger" id="unamealert"></span>
	    <p class="help-block">人生已如此艰难，黑客请高抬贵手</p>
	  </div>
	</div>
	<div class="control-group">
	  <label class="control-label" for="userkey">你的密码</label>
	  <div class="controls">
	    <input type="password" class="input-xlarge span2" id="userkey"> <span class="alert-danger" id="ukeyalert"></span>
	  </div>
	</div>
        <a href="#" class="btn btn-primary offset4" onclick="checkunamenull();">登录</a>
      </fieldset>
    </form>
</div>      
            </div>  
    </body>

</html>
