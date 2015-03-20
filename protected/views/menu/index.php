<h2 class="contentTitle">微信菜单-获取/设置</h2>
<div style=" float:left; display:block; margin:10px; overflow:auto; width:200px; height:500px; border:solid 1px #CCC; line-height:21px; background:#FFF;">
<ul class="tree">
    <?php foreach ($models as $value) {
        $str = "";
        if(!empty($value['sub_button']))
        {
            $str .= '<li><a href="'.Yii::app()->createAbsoluteUrl('menu/update',array("name"=>urlencode($value['name']))).'" target="dialog">'.$value['name'].'</a><ul>';
            foreach ($value['sub_button'] as $val) {
                $str .= '<li><a href="'.Yii::app()->createAbsoluteUrl('menu/update',array("name"=>urlencode($val['name']))).'" target="dialog" rel="main">'.$val['name'].'</a></li>';
            }
            $str .= '</ul></li>';   
        }else
        {
            $str .= '<li><a href="'.Yii::app()->createAbsoluteUrl('menu/update',array("name"=>urlencode($value['name']))).'" target="dialog">'.$value['name'].'</a></li>';
        }
        echo $str;
    }?>
</ul>
</div>
<div class="pageFormContent" layoutH="60">
    <fieldset>
        <dl class="nowrap">
			<dt>获取微信菜单：</dt>
			<dd><div class="buttonActive"><div class="buttonContent"><button id="getMenu">同步微信数据</button></div></div></dd>
		</dl>
        <dl class="nowrap">
			<dt>添加微信菜单：</dt>
			<a href="<?php echo Yii::app()->createAbsoluteUrl('menu/update');?>" target="dialog">添加菜单</a>
		</dl>
        <dl class="nowrap">
			<dt>应用：</dt>
			<dd><div class="buttonActive"><div class="buttonContent"><button id="setMenu">应用</button></div></div><span class="info">设置后24小时才会在客户端显示</span></dd>
		</dl>
    </fieldset>
</div>
<script type="text/javascript">
 /**
 * 从服务器获取微信菜单json数据
 */
$("#getMenu").click(function(){
    $.ajax({    
        type:"POST",    
        dataType:"json",    
        url:"<?php echo Yii::app()->createAbsoluteUrl('menu/get'); ?>",
        success:function(data){ 
            if(data.code==0)
            {
                alertMsg.correct("更新成功");
                navTab.reload();
            }
            else
            {
                alertMsg.error(data.msg);
            }
     }});
});

 /**
 * 将数据推送到微信服务器
 */
$("#setMenu").click(function(){
    var _menu = $("#menutext").text();
    $.ajax({    
        type:"POST",    
        dataType:"json", 
        data:{menu:_menu},
        url:"<?php echo Yii::app()->createAbsoluteUrl('menu/set'); ?>",
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

    
</script>>