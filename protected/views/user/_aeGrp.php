<div class="pageContent">
	<form method="post" action="<?php echo Yii::app()->createAbsoluteUrl('user/grpSave'); ?>" class="pageForm required-validate" onsubmit="return validateCallback(this, viData);">
		<div class="pageFormContent" layoutH="56">
            <p>
				<label>分组名称：</label>
                <input  name="name" type="text" class="textInput readonly" size="30" value="<?php echo empty($models["name"])?"":$models["name"];?>">
                <input name="id" type="hidden" value="<?php echo empty($models["id"])?"":$models["id"];?>"/>
			</p>
		</div>
		<div class="formBar">
			<ul>
				<!--<li><a class="buttonActive" href="javascript:;"><span>保存</span></a></li>-->
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
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
        navTab.reload(json.grouplist);  //刷新主页面
        $.pdialog.closeCurrent();  //
    }
}
</script>