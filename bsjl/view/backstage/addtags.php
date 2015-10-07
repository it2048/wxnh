<div class="pageContent">
    <form method="post" action="<?php echo Application::$_config['home']['url']; ?>/index.php/backstage_addtag.html" class="pageForm required-validate" onsubmit="return validateCallback(this, closeAddtags);">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>姓名：</label>
                <input name="name" type="text" class="required" size="20"/>
            </p>
            <p>
                <label>应聘品牌：</label>
                <select class="" name="brand">
                    <option value="肯德基">肯德基</option>
                    <option value="必胜客">必胜客</option>
                    <option value="必胜宅急送">必胜宅急送</option>
                    <option value="东方既白">东方既白</option>
                    <option value="小肥羊">小肥羊</option>
                </select>
            </p>
            <p>
                <label>目前所在城市：</label>
                <input name="ccity" type="text" class="required" size="20"/>
            </p>
            <p>
                <label>意向工作城市：</label>
                <input name="jcity" type="text" class="required" size="20"/>
            </p>
            <p>
                <label>毕业院校：</label>
                <input name="school" type="text" class="required" size="30"/>
            </p>
            <p>
                <label>手机号码 ：</label>
                <input name="tel" type="text" class="required" size="30"/>
            </p>
            <p>
                <label>电子邮箱：</label>
                <input name="email" type="text" class="required" size="30"/>
            </p>
        </div>
        <div class="formBar">
            <ul>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
                <li>
                    <div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
                </li>
            </ul>
        </div>
    </form>
</div>
<script type="text/javascript">
function closeAddtags(json)
{
    var tmpArr = json.split("|");
    if(tmpArr[0]!=0)
    {
        alertMsg.error(tmpArr[1]); //返回错误
    }
    else
    {
        navTab.reload(json.tagsmanage);  //刷新主页面
        alertMsg.correct(tmpArr[1]); //返回错误
        $.pdialog.closeCurrent();  //
    }

}
</script>