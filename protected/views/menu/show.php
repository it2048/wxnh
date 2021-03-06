<h2 class="contentTitle">微信菜单-获取/设置</h2>
<div style=" float:left; display:block; margin:10px; overflow:auto; width:800px; height:500px; border:solid 1px #CCC; line-height:21px; background:#FFF;">
    <textarea name="json_ttt" id="json_ttt" cols="129" rows="32"></textarea>
</div>
<div class="buttonActive">
    <div class="buttonContent">
        <button id="setMenu">应用</button>
    </div>
</div>


<script type="text/javascript">

    $(function(){

        $.ajax({
            type:"POST",
            dataType:"json",
            url:"<?php echo Yii::app()->createAbsoluteUrl('menu/dget'); ?>",
            success:function(data){
                if(data.code==0)
                {
                    var _model = JSON.stringify(data.data,null,4);
                    $("#json_ttt").val(_model);
                }
            }});


    });

    /**
     * 将数据推送到微信服务器
     */
    $("#setMenu").click(function(){
        var _menu = $("#json_ttt").val();
        $.ajax({
            type:"POST",
            dataType:"json",
            data:{menu:_menu},
            url:"<?php echo Yii::app()->createAbsoluteUrl('menu/dset'); ?>",
            success:function(data){
                if(data.code==0)
                {
                    alertMsg.correct("设置成功");
                }
                else
                {
                    alertMsg.error(data.msg);
                }
            }});
    });


</script>