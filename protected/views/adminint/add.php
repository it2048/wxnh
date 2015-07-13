<div class="pageContent">
    <form method="post" action="<?php echo Yii::app()->createAbsoluteUrl('adminint/save'); ?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, viData);" enctype="multipart/form-data">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>品牌：</label>
                <select class="combox" name="brand">
                    <?php
                    foreach($lst as $k=>$val){
                        printf('<option value="%s">%s</option>',$k,$val);
                    } ?>
                </select>
            </p>
            <p>
                <label>DM：</label>
                <input  name="dm" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>城市：</label>
                <input  name="city" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>HR时间：</label>
                <input  name="hr_time" type="text" class="date" dateFmt="yyyy-MM-dd HH:mm" readonly="true" value="">
            </p>
            <p>
                <label>建议AM：</label>
                <input  name="am_sge" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>AM时间：</label>
                <input  name="am_time" type="text" class="date" dateFmt="yyyy-MM-dd HH:mm" readonly="true" value="">
            </p>
            <p>
                <label>AM地址：</label>
                <input  name="am_add" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>AM人数：</label>
                <input  name="am_people" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>OJE餐厅：</label>
                <input  name="oje_ct" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>OJE开始时间：</label>
                <input  name="oje_time" type="text" class="date" dateFmt="yyyy-MM-dd HH:mm" readonly="true" value="">
            </p>
            <p>
                <label>OJE地址：</label>
                <input  name="oje_add" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>OJE人数：</label>
                <input  name="oje_people" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>DM时间：</label>
                <input  name="dm_time" type="text" class="date" dateFmt="yyyy-MM-dd HH:mm" readonly="true" value="">
            </p>
            <p>
                <label>DM地址：</label>
                <input  name="dm_add" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>DM人数：</label>
                <input  name="dm_people" type="text" class="required textInput" size="30" value="">
            </p>
            <p>
                <label>计划表月份：</label>
                <input  name="month" type="text" class="date" dateFmt="yyyyMM" readonly="true" value="<?php echo date('Ym');?>">
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
            alertMsg.correct("保存成功"); //返回错误
            navTab.reload(json.usermaneger);  //刷新主页面
            $.pdialog.closeCurrent();  //
        }
    }

</script>