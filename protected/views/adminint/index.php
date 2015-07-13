<div class="pageHeader">
    <form  id="pagerForm" onsubmit="return navTabSearch(this);" action="<?php echo Yii::app()->createAbsoluteUrl('adminint/index'); ?>" method="post">
        <div class="searchBar">
            <table class="searchContent">
                <tbody><tr>
                    <td>
                        月份：<input type="text" id="month" name="month" class="date" dateFmt="yyyyMM" readonly="true" value="<?php echo empty($pages['month'])?date('Ym'):$pages['month'];?>"/>
                    </td>
                    <td><div class="buttonActive"><div class="buttonContent"><button type="submit">搜索</button></div></div></td>
                </tr>
                </tbody></table>
        </div>

        <input type="hidden" name="pageNum" value="<?php echo $pages['pageNum'];?>" /><!--【必须】value=1可以写死-->
        <input type="hidden" name="numPerPage" value="50" /><!--【可选】每页显示多少条-->
    </form>
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
            <li><a class="add" height="500" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('adminint/add');?>"><span>添加</span></a></li>
		</ul>
	</div>
	<table class="table" width="1100" layoutH="76">
		<thead>
			<tr>
				<th width="80">品牌</th>
				<th width="80">DM</th>
                <th width="80">招募专员</th>
                <th width="80">城市</th>
                <th width="80">HR时间</th>
                <th width="80">建议AM</th>
                <th width="80">AM时间</th>
                <th width="80">AM地点</th>
                <th width="50">AM预估人数</th>
                <th width="80">OJE餐厅</th>
                <th width="80">OJE开始时间</th>
                <th width="80">OJE地点</th>
                <th width="50">OJE预估人数</th>
                <th width="80">DM时间</th>
                <th width="80">DM地点</th>
                <th width="50">DM预估人数</th>
                <th width="80">月份</th>
				<th width="70">操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($models as $value) {?>
			<tr>
				<td><?php echo empty($lst[$value['brand']])?$value['brand']:$lst[$value['brand']]; ?></td>
                <td><?php echo $value['dm'];?></td>
                <td><?php echo empty($userList[$value['zmzy']])?$value['zmzy']:$userList[$value['zmzy']];?></td>
                <td><?php echo $value['city'];?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['hr_time']); ?></td>
                <td><?php echo $value['am_sge'];?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['am_time']); ?></td>
                <td><?php echo $value['am_add'];?></td>
                <td><?php echo $value['am_people'];?></td>
                <td><?php echo $value['oje_ct'];?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['oje_time']);?></td>
                <td><?php echo $value['oje_add']; ?></td>
                <td><?php echo $value['oje_people'];?></td>
                <td><?php echo date('Y-m-d H:i:s',$value['dm_time']);?></td>
                <td><?php echo $value['dm_add'];?></td>
                <td><?php echo $value['dm_people'];?></td>
                <td><?php echo $value['month'];?></td>
				<td>
                    <a title="确实要删除这条记录吗?" callback="deleteAuCall" target="ajaxTodo" href="<?php echo Yii::app()->createAbsoluteUrl('adminint/del',array('id'=>$value['id'])); ?>" class="btnDel">删除</a>
                    <a title="编辑" mask="true" height="500" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('adminint/edit',array("id"=>$value['id'])); ?>" class="btnEdit">编辑</a>
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
function updateUsr(json) {
    if(json.code!=0)
    {
        alertMsg.error(json.msg); //返回错误
    }
    else
    {
        alertMsg.correct("更新成功"); //返回错误
        navTab.reload(json.userlist);  //刷新主页面
    }
}

function deleteAuCall(res)
{
    if(res.code!=0)
        alertMsg.error("删除失败");
    else
    {
        navTab.reload(res.mobile_game_config);  //刷新主页面
    }

}
</script>