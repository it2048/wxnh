<div class="pageContent">
	<form method="post" action="<?php echo Yii::app()->createAbsoluteUrl('menu/menuSave'); ?>" class="pageForm required-validate" onsubmit="return validateCallback(this, viData);">
		<div class="pageFormContent" layoutH="56">
            <p>
				<label>分组名称：</label>
                <input  name="name" type="text" class="textInput" size="30" value="<?php echo empty($models["name"])?"":$models["name"];?>">
                <input  name="nametp" type="hidden" value="<?php echo empty($models["name"])?"":$models["name"];?>">
			</p>
            <p>
                <label>类型：</label>
				<select name="type" class="required combox">
                    <option value="0" <?php echo !empty($models["name"])&&$models['type']==0?"selected":""; ?>>父类</option>
					<option value="1" <?php echo !empty($models["name"])&&$models['type']==1?"selected":""; ?>><?php echo Menu::$TYPE[1]; ?></option>
					<option value="2" <?php echo !empty($models["name"])&&$models['type']==2?"selected":""; ?>><?php echo Menu::$TYPE[2]; ?></option>
				</select>
            </p>
            <p>
                <label>数据：</label>
				<input  name="obj" type="text" class="textInput" size="30" value="<?php echo empty($models["obj"])?"":$models["obj"];?>">
            </p>
            <p>
                <label>父标题：</label>
				<select name="parent" class="required combox">
                    <option value="0">无父标题</option>
                    <?php
                            foreach ($parent as $value) {?>
                        <option value="<?php echo empty($models["name"])?"":$value["name"];?>" <?php echo !empty($models["name"])&&$models['parent']==$value['name']?"selected":""; ?>><?php echo $value["name"];?></option>
                     <?php }?>
				</select>
            </p>
		</div>
		<div class="formBar">
			<ul>
				<!--<li><a class="buttonActive" href="javascript:;"><span>保存</span></a></li>-->
                <?php  if(!empty($models["name"])){?>
                <li><div class="button"><div class="buttonContent"><button type="button" onclick="del();">删除</button></div></div></li>
                <?php }?>
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
        navTab.reload(json.menuset);  //刷新主页面
        $.pdialog.closeCurrent();  //
    }
}
/**
 * 删除
 */
function del() {
    $.ajax({    
        type:"POST",    
        dataType:"json", 
        data:{name:"<?php echo empty($models["name"])?"":urlencode($models["name"]);?>"},
        url:"<?php echo Yii::app()->createAbsoluteUrl('menu/del'); ?>",
        success:function(data){ 
            if(data.code==0)
            {
                alertMsg.correct("删除成功");
                navTab.reload(data.menuset);  //刷新主页面
                $.pdialog.closeCurrent(); 
            }
            else
            {
                alertMsg.error(data.msg);
            }
     }});
    
}
</script>