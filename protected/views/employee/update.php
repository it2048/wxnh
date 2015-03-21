<div class="pageContent">
	<form method="post" action="<?php echo Yii::app()->createAbsoluteUrl('user/save'); ?>" class="pageForm required-validate" onsubmit="return validateCallback(this, viData);">
		<div class="pageFormContent" layoutH="56">
			<p>
				<label>用户昵称：</label>
                <input readonly="true" name="nickname" type="text" class="textInput readonly" size="30" value="<?php echo empty($usrInfo["nickname"])?"":$usrInfo["nickname"];?>">
                <input name="open_id" type="hidden" value="<?php echo empty($usrInfo["open_id"])?"":$usrInfo["open_id"];?>"/>
			</p>
            <p>
				<label>Email：</label>
                <input name="email" type="text" class="textInput" size="30" value="<?php echo empty($usrInfo["email"])?"":$usrInfo["email"];?>">
			</p>
            <p>
				<label>姓名：</label>
				<input name="name" class="textInput" type="text" size="30" value="<?php echo empty($usrInfo["name"])?"":$usrInfo["name"];?>"/>
			</p>
            <p>
				<label>员工编号：</label>
				<input name="employee_id" class="textInput" type="text" size="30" value="<?php echo empty($usrInfo["employee_id"])?"":$usrInfo["employee_id"];?>"/>
			</p>
            <p>
				<label>用户电话：</label>
				<input name="tel" class="textInput" type="text" size="30" value="<?php echo empty($usrInfo["tel"])?"":$usrInfo["tel"];?>"/>
			</p>
            <p>
				<label>所在分组：</label>
                <select class="" name="group_id">
                    <?php 
                                foreach ($grpList as $value) {
                                    $sel = $value['id']==$usrInfo['group_id']?"selected":"";
                                    echo '<option value="'.$value['id'].'" '.$sel.'>'.$value['name'].'</option>';
                                }
                            ?>
                </select>
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
        navTab.reload(json.grouplist);  //刷新主页面
        alertMsg.correct("更新成功"); //返回错误
        $.pdialog.closeCurrent();  //
    }
}
</script>