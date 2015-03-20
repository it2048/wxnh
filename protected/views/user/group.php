<form id="pagerForm" action="<?php echo Yii::app()->createAbsoluteUrl('user/group'); ?>" method="post">
    <input type="hidden" name="pageNum" value="<?php echo $pages['pageNum'];?>" /><!--【必须】value=1可以写死-->
    <input type="hidden" name="numPerPage" value="<?php echo $pages['numPerPage'];?>" /><!--【可选】每页显示多少条-->
    <input type="hidden" name="orderField" value="<?php echo $pages['orderField']; ?>" />
    <input type="hidden" name="orderDirection" value="<?php echo $pages['orderDirection']; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
            <li><a class="add" target="ajaxTodo" callback="updateGrp" href="<?php echo Yii::app()->createAbsoluteUrl('user/updateGrp');?>"><span>同步用户分组</span></a></li>
            <li><a class="add" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('user/editGrp');?>"><span>添加分组</span></a></li>
		</ul>
	</div>
	<table class="table" width="400" layoutH="74">
		<thead>
			<tr>
				<th width="80" orderField="id" class="desc">分组编号</th>
				<th width="280" orderField="name">分组名称</th>
                <th width="40">编辑</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($models as $value) {?>
			<tr id="<?php echo $value['id']; ?>">
				<td><?php echo $value['id']; ?></td>
                <td><?php echo $value['name'];?></td>
				<td>
					<a title="编辑" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('user/editGrp',array("id"=>$value['id'])); ?>" class="btnEdit">编辑</a>
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
<script type="text/javascript">
/**
 * 回调函数
 */
function updateGrp(json) {
    if(json.code!=0)
    {
        alertMsg.error(json.msg); //返回错误
    }
    else
    {
        alertMsg.correct("更新成功"); //返回错误
        navTab.reload(json.grouplist);  //刷新主页面
    }
}
</script>