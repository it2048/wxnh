<div class="pageHeader">
    <form  id="pagerForm" onsubmit="return navTabSearch(this);" action="<?php echo Yii::app()->createAbsoluteUrl('admincontent/usermanager'); ?>" method="post">
        <div class="searchBar">
            <table class="searchContent">
                <tbody><tr>
                    <td>
                        用户名：<input type="text" name="name" class="textInput" value="<?php echo $pages['name'];?>">
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
            <li><a class="add" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('admincontent/useradd');?>"><span>添加</span></a></li>
        </ul>
    </div>
    <table class="table" width="460" layoutH="110">
        <thead>
        <tr>
            <th width="100">用户名</th>
            <th width="200">电话</th>
            <th width="200">邮箱</th>
            <th width="160">编辑</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($models as $value) {?>
            <tr>
                <td><?php echo $value['username']; ?></td>
                <td><?php echo $value['tel']; ?></td>
                <td><?php echo $value['email']; ?></td>
                <td>
                    <a title="确实要删除这条记录吗?" callback="deleteAuCall" target="ajaxTodo" href="<?php echo Yii::app()->createAbsoluteUrl('admincontent/userdelete',array('username'=>$value['username'])); ?>" class="btnDel">删除</a>
                    <a title="编辑" target="dialog" href="<?php echo Yii::app()->createAbsoluteUrl('admincontent/useredit',array('username'=>$value['username'])); ?>" class="btnEdit">编辑</a>
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