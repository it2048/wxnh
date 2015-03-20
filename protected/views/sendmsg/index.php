<form id="pagerForm" action="<?php echo Yii::app()->createAbsoluteUrl('sendmsg/index'); ?>" method="post">
    <input type="hidden" name="pageNum" value="<?php echo $pages['pageNum'];?>" /><!--【必须】value=1可以写死-->
    <input type="hidden" name="numPerPage" value="<?php echo $pages['numPerPage'];?>" /><!--【可选】每页显示多少条-->
    
    <input type="hidden" name="send_id" value="<?php echo $pages['send_id'];?>" />
    <input type="hidden" name="receive_id" value="<?php echo $pages['receive_id'];?>" />
    <input type="hidden" name="content" value="<?php echo $pages['content'];?>" />

    <input type="hidden" name="tmstart" value="<?php echo $pages['tmstart'];?>" />
    <input type="hidden" name="tmstop" value="<?php echo $pages['tmstop'];?>" />

</form>
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="<?php echo Yii::app()->createAbsoluteUrl('sendmsg/index'); ?>" method="post">
        <input type="hidden" name="<?php echo Yii::app()->request->csrfTokenName;?>" value="<?php echo Yii::app()->request->csrfToken;?>" />
	<div class="searchBar">
		<table class="searchContent">
			<tbody>
            <tr>
                <td>
					开始时间：<input type="text" id="tmstart" name="tmstart" class="date" dateFmt="yyyy-MM-dd HH:mm:ss" readonly="true" value="<?php echo $pages['tmstart'];?>"/>
				</td>
                <td>
					结束时间：<input type="text" id="tmstop" name="tmstop" class="date" dateFmt="yyyy-MM-dd HH:mm:ss" readonly="true" value="<?php echo $pages['tmstop'];?>"/>
				</td>
			</tr>
            <tr>
                <td>
					发送帐号：<input type="text" name="send_id" class="textInput" value="<?php echo $pages['send_id'];?>">
				</td>
				<td>
					接收帐号：<input type="text" name="receive_id" class="textInput" value="<?php echo $pages['receive_id'];?>">
				</td>
                <td>
					关键字匹配：<input type="text" name="content" class="textInput" value="<?php echo $pages['content'];?>">
				</td>
                <td><div class="buttonActive"><div class="buttonContent"><button type="submit">搜索</button></div></div></td>
			</tr>
		</tbody></table>
	</div>
	</form>
</div>
<div class="pageContent">
	<table class="table" width="900" layoutH="76">
		<thead>
			<tr>
				<th width="140">时间</th>
				<th width="80">发送方</th>
                <th width="80">接收方</th>
                <th width="530">内容</th>
				<th width="70">操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($msgList as $value) {?>
			<tr>
                <td><?php echo date("Y-m-d H:i:s",$value['tm']); ?></td>
				<td title="<?php $fu = empty($nameList[$value['send_id']])?$value['send_id']:$nameList[$value['send_id']]."(".$value['send_id'].")"; echo $fu;?>"><?php echo $fu; ?></td>
                <td title="<?php $tu = empty($nameList[$value['receive_id']])?$value['receive_id']:$nameList[$value['receive_id']]."(".$value['receive_id'].")"; echo $tu;?>"><?php echo $tu; ?></td>
                <td title="<?php echo $value["content"];?>"><?php echo mb_substr($value["content"],0,50,"utf-8");?></td>
				<td>
					<a title="回复" mask="true" height="520" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('sendmsg/send',array("send_id"=>$value['send_id'],"receive_id"=>$value['receive_id'])); ?>" class="btnView">回复</a>
                </td>
			</tr>
            <?php }?>
		</tbody>
	</table>
	<div class="panelBar">
            <div class="pages">
                <span>共<?php echo $pages['countPage'];?>条</span>
            </div>        
        <div class="pagination" targetType="navTab" totalCount="<?php echo $pages['countPage'];?>" numPerPage="<?php echo $pages['numPerPage'];?>" pageNumShown="<?php echo $pages['numPerPage'];?>" currentPage="<?php echo $pages['pageNum'];?>"></div>
	</div>
</div>