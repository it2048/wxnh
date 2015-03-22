<form id="pagerForm" action="<?php echo Yii::app()->createAbsoluteUrl('user/index'); ?>" method="post">
    <input type="hidden" name="pageNum" value="<?php echo $pages['pageNum'];?>" /><!--【必须】value=1可以写死-->
    <input type="hidden" name="numPerPage" value="<?php echo $pages['numPerPage'];?>" /><!--【可选】每页显示多少条-->
    <input type="hidden" name="orderField" value="<?php echo $pages['orderField']; ?>" />
    <input type="hidden" name="orderDirection" value="<?php echo $pages['orderDirection']; ?>" />
</form>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
            <li><a class="add" target="ajaxTodo" callback="updateUsr" href="<?php echo Yii::app()->createAbsoluteUrl('user/updateuesrs');?>"><span>批量同步微信数据</span></a></li>
            <li><a class="add" target="ajaxTodo" callback="updateUsr" href="<?php echo Yii::app()->createAbsoluteUrl('user/refwx');?>"><span>更新微信访问标识</span></a></li>
            <li><a class="add" target="ajaxTodo" callback="updateUsr" href="<?php echo Yii::app()->createAbsoluteUrl('user/getNewUser');?>"><span>获取关注人列表</span></a></li>
		</ul>
	</div>
	<table class="table" width="900" layoutH="76">
		<thead>
			<tr>
				<th width="100">微信昵称</th>
				<th width="100">分组名</th>
                <th width="120">姓名</th>
                <th width="180">邮箱</th>
                <th width="100">电话</th>
                <th width="100">员工编号</th>
                <th width="66">状态</th>
                <th width="100">是否在职</th>
				<th width="70">操作</th>
			</tr>
		</thead>
		<tbody>
            <?php foreach ($usrList as $value) {?>
			<tr id="<?php echo $value['open_id']; ?>">
				<td><?php echo $value['nickname']; ?></td>
                <td><?php echo empty($grpList[$value['group_id']])?"不存在":$grpList[$value['group_id']];?></td>
                <td><?php echo $value['name'];?></td>
                <td><?php echo $value['email'];?></td>
                <td><?php echo $value['tel'];?></td>
                <td><?php echo $value['employee_id'];?></td>
                <td><?php echo $value['subscribe']==1?"关注":"未关注";?></td>
                <td><?php echo empty($value['emp_name'])?"<span style='color:red;'>离职</span>":"在职";?></td>
				<td>
					<a title="同步微信数据" callback="updateUsr" target="ajaxTodo" href="<?php 
                    echo Yii::app()->createAbsoluteUrl('user/getfrmwx',array("openid"=>$value['open_id'])); ?>" class="btnView">同步微信数据</a>
                    <?php if($value['subscribe']==1) {?>
					<a title="编辑" mask="true" height="320" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('user/update',array("openid"=>$value['open_id'])); ?>" class="btnEdit">编辑</a>
                    <?php }?>
                    <?php if($value['subscribe']==0) {?>
                        <a title="删除" callback="updateUsr" target="ajaxTodo" href="<?php echo Yii::app()->createAbsoluteUrl('user/del',array("openid"=>$value['open_id'])); ?>" class="btnDel">删除</a>
                    <?php }?>
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
</script>