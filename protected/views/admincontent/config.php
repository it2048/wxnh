<h2 class="contentTitle">城市与品牌设置(保存按钮在右下角)</h2>
<div class="pageContent">
    <form method="post" action="<?php echo Yii::app()->createAbsoluteUrl('admincontent/confsave'); ?>" class="pageForm required-validate" onsubmit="return validateCallback(this,viData)">
        <div class="pageFormContent nowrap" layoutH="97">
            <dl>
                <dt>需要OJE的城市</dt>
                <dd>
                    <input type="text" name="city" maxlength="120" size="90" value="<?php echo $city?>"/>
                </dd>
            </dl>
            <dl>
                <dt>品牌列表</dt>
                <dd>
                    <input type="text" name="brand" maxlength="120" size="90" value="<?php echo $brand?>"/>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>
    </form>

</div>
<script type="text/javascript">
    /**
     * 回调函数
     */
    function viData(json) {
        if(json.code!=0)
        {
            alertMsg.error(json.msg); //返回错误
        }
        else
        {
            alertMsg.correct("更新成功"); //返回错误
            navTab.reload(json.usermaneger);  //刷新主页面
        }
    }

</script>
