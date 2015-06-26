<div class="pageHeader">
    <form  id="pagerForm" onsubmit="return navTabSearch(this);" action="<?php echo Yii::app()->createAbsoluteUrl('employee/index'); ?>" method="post">
        <div class="searchBar">
            <table class="searchContent">
                <tbody><tr>
                    <td>
                        员工姓名：<input type="text" name="name" class="textInput" value="<?php echo $pages['name'];?>">
                        当前面试阶段：<input type="text" name="stage" class="textInput" value="<?php echo $pages['stage'];?>">
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
            <li><a title="导入数据" mask="true" height="200" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('employee/vimport'); ?>" class="add"><span>导入数据</span></a></li>
        </ul>
    </div>
    <table class="table" width="960" layoutH="110">
        <thead>
        <tr>
            <th width="100">姓名</th>
            <th width="100">电话</th>
            <th width="100">邮箱</th>
            <th width="160">应聘职位名称</th>
            <th width="100">当前招聘进度</th>
            <th width="200">额外通知内容</th>
            <th width="100">编辑</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($models as $value) {?>
            <tr>
                <td><?php echo $value['employee_name']; ?></td>
                <td><?php echo $value['tel']; ?></td>
                <td><?php echo $value['email']; ?></td>
                <td><?php echo $value['empty_name']; ?></td>
                <td><?php echo $value['stage']; ?></td>
                <td><?php echo empty($hook[$value['tel']])?"":$hook[$value['tel']]; ?></td>
                <td>
                    <a title="确实要删除这条记录吗?" callback="deleteAuCall" target="ajaxTodo" href="<?php echo Yii::app()->createAbsoluteUrl('employee/del',array('id'=>$value['id'])); ?>" class="btnDel">删除</a>
                    <a title="添加通知" mask="true" height="320" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('employee/hook',array("tel"=>$value['tel'],"stage"=>urlencode($value['stage']))); ?>" class="btnEdit">添加通知项</a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
    <div class="panelBar">
        <div class="pages">
            <span>共<?php echo $pages['countPage'];?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $pages['countPage'];?>" numPerPage="<?php echo $pages['numPerPage'];?>" pageNumShown="10" currentPage="<?php echo $pages['pageNum'];?>"></div>
    </div>
</div>
<script type="text/javascript">
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