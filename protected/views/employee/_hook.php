<div class="pageContent">
    <form method="post" action="<?php echo Yii::app()->createAbsoluteUrl('employee/hooksave'); ?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, viData);" enctype="multipart/form-data">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>用户电话：</label>
                <input  name="hkid" type="hidden" value="<?php echo $id;?>">
                <input readonly="true"  name="hktel" type="text" class="required textInput readonly" size="30" value="<?php echo $tel;?>">
            </p>
            <p>
                <label>通知阶段：</label>
                <input  name="hkstage" type="text" class="required textInput" size="30" value="<?php echo $stage;?>">
            </p>
            <p>
                <label>通知内容：</label>
                <textarea name="hkdesc" cols="30" rows="6" class="textInput"><?php echo $desc;?></textarea>
            </p>
        </div>
        <div class="formBar">
            <ul>
                <!--<li><a class="buttonActive" href="javascript:;"><span>保存</span></a></li>-->
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
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
            $.pdialog.closeCurrent();  //
        }
    }

</script>