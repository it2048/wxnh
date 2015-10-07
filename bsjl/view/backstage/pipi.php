<div class="pageHeader">
    <form id="pagerForm" onsubmit="return navTabSearch(this, 'artpush');" method="post" action="<?php echo Application::$_config['home']['url']; ?>/index.php/backstage_registration.html" >
        <div class="searchBar">
            <table class="searchContent">
                <tbody><tr>
                        <td>
                            开始时间：<input type="text" name="stime" class="date textInput readonly valid" datefmt="yyyy-MM-dd HH:mm:ss"
                                        value="<?php echo empty($pages['stime'])?date("Y-m-d H:i:s",strtotime("-7 day")):$pages['stime'];?>" readonly="true">
                            <input type="hidden" name="pageNum" value="<?php echo $pages['pageNum']; ?>" />
    <input type="hidden" name="numPerPage" value="40" />
                        </td>
                        <td>
                            结束时间：<input type="text" name="etime" class="date textInput readonly valid" datefmt="yyyy-MM-dd HH:mm:ss"
                                        value="<?php echo empty($pages['etime'])?date("Y-m-d H:i:s",time()):$pages['etime'];?>" readonly="true">
                        </td>
                        <td>
                            <button  type="submit">检索</button>
                            <button  type="button" onclick="tmck();">导出</button>
                        </td>
                    </tr>
                </tbody></table>
        </div>
    </form>
</div>
<form id="tmp" method="post" action="<?php echo Application::$_config['home']['url']; ?>/index.php/backstage_explore.html" >
    <div class="searchBar">
        <input type="hidden" name="sttime"/>
        <input type="hidden" name="edttime"/>
    </div>
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="add" height="340" href="<?php echo Application::$_config['home']['url']; ?>/index.php/backstage_addtags.html" target="dialog" rel="dlg_page8" mask="true"><span>添加</span></a></li>
            <li><a title="确实要删除这些记录吗?" target="selectedTodo" postType="string" callback="batdelTags" rel="ids" href="<?php echo Application::$_config['home']['url']; ?>/backstage_batdeltags.html" class="delete"><span>批量删除</span></a></li>
        </ul>
    </div>
    <table class="table" width="1000" layoutH="112">
        <thead>
            <tr>
                <th width="20"><input type="checkbox" group="ids" class="checkboxCtrl"></th>
                <th width="30">编号</th>
                <th width="50">姓名</th>
                <th width="60">应聘品牌</th>
                <th width="80">目前所在城市</th>
                <th width="80">意向工作城市</th>
                <th width="150">毕业院校</th>
                <th width="100">手机号码</th>
                <th width="150">电子邮箱</th>
                <th width="120">添加时间</th>
                <th width="50">编辑</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tags as $key => $val) { ?>
                <tr target="sid_user" rel="1">
                    <td><input name="ids" value="<?php echo $val["id"]; ?>" type="checkbox"></td>
                    <td><?php echo $val["id"]; ?></td>
                    <td><?php echo $val["name"]; ?></td>
                    <td><?php echo $val["brand"]; ?></td>
                    <td><?php echo $val["ccity"]; ?></td>
                    <td><?php echo $val["jcity"]; ?></td>
                    <td><?php echo $val["school"]; ?></td>
                    <td><?php echo $val["tel"]; ?></td>
                    <td><?php echo $val["email"]; ?></td>
                    <td><?php echo date("Y-m-d H:i:s", $val["tm"]); ?></td>
                    <td>
                        <a title="删除" target="ajaxTodo" callback="delTags" href="<?php echo Application::$_config['home']['url'] . "/backstage_deltags_" . $val["id"] . ".html"; ?>" class="btnDel">删除</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="panelBar">
        <div class="pages">
            共<?php echo $pages['countPage']; ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo $pages['countPage']; ?>" numPerPage="40" pageNumShown="40" currentPage="<?php echo $pages['pageNum']; ?>"></div>
    </div>
</div>
<script type="text/javascript">
    function delTags(json)
    {
        var tmpArr = json.split("|");
        if (tmpArr[0] != 0)
        {
            alertMsg.error(tmpArr[1]); //返回错误
        }
        else
        {
            navTab.reload(json.tagsmanage);  //刷新主页面
            alertMsg.correct(tmpArr[1]); //返回正确信息
        }

    }
    function batdelTags(json)
    {
        var tmpArr = json.split("|");
        if (tmpArr[0] != 0)
        {
            alertMsg.error(tmpArr[1]); //返回错误
        }
        else
        {
            navTab.reload(json.tagsmanage);  //刷新主页面
            alertMsg.correct(tmpArr[1]); //返回正确信息
        }

    }
    function tmck(json) {
        var stime = $("input[name='stime']").val();
        var etime = $("input[name='etime']").val();
        $("input[name='sttime']").val(stime);
        $("input[name='edttime']").val(etime);
        alertMsg.correct("导出文件为.csv格式,可使用Excel打开");
        $("#tmp").submit();
    }
</script>