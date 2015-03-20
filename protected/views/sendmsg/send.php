<div class="pageContent">
		<div class="pageFormContent" layoutH="56">
            <p>
                聊天记录:<?php echo $usr['name']."-".$usr['nickname']."-".$usr['open_id'];  ?>
                <textarea id="hismsg" readonly="true" cols="80" rows="23" class="textInput readonly"><?php echo "\n".trim($msgStr);?></textarea>
                <textarea id="currmsg" cols="80" rows="4" class="textInput"></textarea>
			</p>
		</div>
		<div class="formBar">
            <ul><li><div class="button"><div class="buttonContent"><button type="button" onclick="lunxun();">刷新</button></div></div></li>
			<ul><li><div class="button"><div class="buttonContent"><button type="button" onclick="send();">发送</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
        <input  id="ltm" type="hidden" value="<?php echo $max; ?>">
</div>

<script type="text/javascript">
/**
 * 回调函数
 */
function send() {
    
    var _cntt = $("#currmsg").val();
    var _hitt = $("#hismsg").val();
    $.ajax({    
        type:"POST",    
        dataType:"json",
        data:{open_id:"<?php echo $usr['open_id'];?>",content:_cntt},
        url:"<?php echo Yii::app()->createAbsoluteUrl('sendmsg/sendUsr'); ?>",
        success:function(data){ 
            if(data.code==0)
            {
                $("#currmsg").val("");
                $("#hismsg").val(_hitt+"\n\n我："+_cntt);
                $("#ltm").val(data.obj);
            }
            else
            {
                alertMsg.error(data.msg);
            }
     }});
}

function lunxun() {
    var _lst = $("#ltm").val();
    var _hitt = $("#hismsg").val();
    if(_lst!="")
    {
        $.ajax({    
            type:"POST",    
            dataType:"json",    
            data:{lst:_lst,open_id:"<?php echo $usr['open_id'];?>",tname:"<?php echo $tname;?>"},
            url:"<?php echo Yii::app()->createAbsoluteUrl('sendmsg/sendMe'); ?>",  
            success:function(data){ 
                if(data.code==0)
                {
                    $("#ltm").val(data.obj.tm);
                    $("#hismsg").val(_hitt+"\n\n"+data.obj.txt);
                }
         }});
     }
}
</script>