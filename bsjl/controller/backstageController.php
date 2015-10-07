<?php

/**
 * 后台管理控制器
 * @copyright   Copyright(c) 2013
 * @author      xiongfanglei <jsjscool@163.com/sibenx.com> 
 * @version     1.0 
 */
class backstageController extends Pt{

    //显示后台首页信息
    public function indexAction()
    {
        $this->showTemplate('backstage/index'); 
    }

    public function registrationAction()
    {
        //先获取当前是否有页码信息
        $pages['pageNum'] = empty($_POST["pageNum"])?"1":$_POST["pageNum"]; //当前页
        $pages['countPage'] = empty($_POST["countPage"])?"0":$_POST["countPage"];  //总共多少记录
        
        $pages['stime'] = empty($_POST["stime"])?"":$_POST["stime"];  //开始时间
        $pages['etime'] = empty($_POST["etime"])?"":$_POST["etime"];  //结束时间
        $condition = "";
        $condition .= empty($pages['stime'])?"":sprintf("and tm>'%s'",strtotime($pages['stime']));
        $condition .= empty($pages['etime'])?"":sprintf("and tm<='%s'",strtotime($pages['etime']));
        $offect = 40;
        $limit = 40 * ($pages['pageNum'] - 1);
        $Tags = $this->model('Pipi');
        if (empty($pages['countPage']))
            $pages['countPage'] = $Tags->count($condition);
        
        $TagsData = $Tags->getAll($limit,$offect,$condition);
        $data['tags'] = $TagsData;
        $data['pages'] = $pages;
        $this->showTemplate('backstage/pipi',$data);
    }
    
    public function recommendAction()
    {
        //先获取当前是否有页码信息
        $pages['pageNum'] = empty($_POST["pageNum"])?"1":$_POST["pageNum"]; //当前页
        $pages['countPage'] = empty($_POST["countPage"])?"0":$_POST["countPage"];  //总共多少记录
        
        $pages['stime'] = empty($_POST["stime"])?"":$_POST["stime"];  //开始时间
        $pages['etime'] = empty($_POST["etime"])?"":$_POST["etime"];  //结束时间
        $condition = "";
        $condition .= empty($pages['stime'])?"":sprintf(" and tm>'%s'",strtotime($pages['stime']));
        $condition .= empty($pages['etime'])?"":sprintf(" and tm<='%s'",strtotime($pages['etime']));
        $offect = 40;
        $limit = 40 * ($pages['pageNum'] - 1);
        $Tags = $this->model('Pipitj');
        if (empty($pages['countPage']))
            $pages['countPage'] = $Tags->count($condition);
        
        $TagsData = $Tags->getAll($limit,$offect,$condition);
        $data['tags'] = $TagsData;
        $data['pages'] = $pages;
        $this->showTemplate('backstage/pipitj',$data);
    }
    
        //导出csv文件
    public function exploretjAction()
    {
        //先获取当前是否有页码信息
        $pages['stime'] = empty($_POST["sttime"])?"":$_POST["sttime"];  //开始时间
        $pages['etime'] = empty($_POST["edttime"])?"":$_POST["edttime"];  //结束时间

        $condition = "";
        $condition .= empty($pages['stime'])?"":sprintf("and tm>'%s'",strtotime($pages['stime']));
        $condition .= empty($pages['etime'])?"":sprintf("and tm<='%s'",strtotime($pages['etime']));
        
        $Tags = $this->model('Pipitj');
        $TagsData = $Tags->getAllno($condition);
        
        // 输出Excel文件头，可把user.csv换成你要的文件名  
        header('Content-Type: application/vnd.ms-excel');  
        header('Content-Disposition: attachment;filename="用户信息.csv"');  
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        // 输出Excel列名信息  
        $head = array("ID","伯乐名字","伯乐岗位","伯乐电话","千里马名字","千里马电话","千里马城市","添加时间");   
        foreach ($head as $i => $v) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码  
            $head[$i] = iconv('utf-8', 'gbk', $v);  
        }
        // 将数据通过fputcsv写到文件句柄  
        fputcsv($fp, $head);
        
        // 计数器  
        $cnt = 0;  
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小  
        $limit = 100000;  

        // 逐行取出数据，不浪费内存  
        $count = count($TagsData);
        for($t=0;$t<$count;$t++) {
            $cnt ++;  
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题  
                ob_flush();  
                flush();  
                $cnt = 0;  
            }  
            $row = $TagsData[$t]; 
            foreach ($row as $i => $v) {  
                if($i=="tm")$v=date("Y-m-d H:i:s",$v);
                $row[$i] = iconv('utf-8', 'gbk', $v);  
            }  
            fputcsv($fp, $row);  
        }
    }
    
    
    //导出csv文件
    public function exploreAction()
    {
        //先获取当前是否有页码信息
        $pages['stime'] = empty($_POST["sttime"])?"":$_POST["sttime"];  //开始时间
        $pages['etime'] = empty($_POST["edttime"])?"":$_POST["edttime"];  //结束时间

        $condition = "";
        $condition .= empty($pages['stime'])?"":sprintf("and tm>'%s'",strtotime($pages['stime']));
        $condition .= empty($pages['etime'])?"":sprintf("and tm<='%s'",strtotime($pages['etime']));
        
        $Tags = $this->model('Pipi');
        $TagsData = $Tags->getAllno($condition);
        
        // 输出Excel文件头，可把user.csv换成你要的文件名  
        header('Content-Type: application/vnd.ms-excel');  
        header('Content-Disposition: attachment;filename="用户信息.csv"');  
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        // 输出Excel列名信息  
        $head = array("ID","姓名","应聘品牌","目前所在城市","意向工作城市","毕业院校","手机号码","电子邮箱","添加时间");  
        foreach ($head as $i => $v) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码  
            $head[$i] = iconv('utf-8', 'gbk', $v);  
        }
        // 将数据通过fputcsv写到文件句柄  
        fputcsv($fp, $head);
        
        // 计数器  
        $cnt = 0;  
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小  
        $limit = 100000;  

        // 逐行取出数据，不浪费内存  
        $count = count($TagsData);
        for($t=0;$t<$count;$t++) {
            $cnt ++;  
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题  
                ob_flush();  
                flush();  
                $cnt = 0;  
            }  
            $row = $TagsData[$t]; 
            foreach ($row as $i => $v) {  
                if($i=="tm")$v=date("Y-m-d H:i:s",$v);
                $row[$i] = iconv('utf-8', 'gbk', $v);  
            }  
            fputcsv($fp, $row);  
        }
    }
    //添加标签
    public function addtagsAction()
    {
        $this->showTemplate('backstage/addtags');
    }
    //编辑标签
    public function edittagsAction($id)
    {
        if(empty($id[0]))
        {
            Application::showError("壮士，你走错地方了……");
        }
        else
        {
            $Tags = $this->model('Tags');
            $tag = $Tags->getRow($id[0]);
            if(empty($tag))
                Application::showError("壮士，你走错地方了……");
            else
            {
                $data['tag'] = $tag;
                $this->showTemplate('backstage/edittags',$data);
            }
        }

    }
     public function addtagAction(){
        $msg = "1|添加错误";
        $name = !empty($_POST['name'])? trim($_POST['name']):"";
        $brand = !empty($_POST['brand'])? trim($_POST['brand']):"50";
        $ccity = !empty($_POST['ccity'])? trim($_POST['ccity']):"";
        $jcity = !empty($_POST['jcity'])? trim($_POST['jcity']):"";
        $school = !empty($_POST['school'])? trim($_POST['school']):"";
        $tel = !empty($_POST['tel'])? trim($_POST['tel']):"1";
        $email = !empty($_POST['email'])? trim($_POST['email']):"0";
       
        //判断文章的必填项是否没填
        if(!empty($name)&&!empty($brand)&&
           !empty($ccity)&&!empty($jcity)&&
           !empty($tel)&&!empty($email))
        {
            $Article = $this->model('Pipi');
         
            $artArry = array(
                "name" =>$name,"brand" => $brand,"ccity" => $ccity,
                "jcity" => $jcity,"school" => $school,"tel" => $tel,
                "email" => $email,"tm"=>time()
            );
            if($pig=$Article->insertRow($artArry))
                $msg = "0|添加简历成功|".$pig;
            else
                $msg = "1|添加简历失败";       
        }
        else
        {
            $msg = "1|存在必填项为空";
        }
        echo json_encode($msg);
    }
    public function deltagsAction($id) {
        
        $msg = "1|删除失败";
        if(empty($id[0]))
        {
            $msg = "1|编号不能为空";
        }
        else
        {
            $Tags = $this->model('Pipi');
            if($Tags->deleteRow($id[0]))
                $msg = "0|删除成功";
            else
                $msg = "1|请检编号是否存在";
        }
        echo json_encode($msg);
    }
        //批量删除
    public function batdeltagsAction() {
        $msg = "1|错误";
        $ids = !empty($_POST['ids'])?mysql_escape_string(trim($_POST['ids'])):"";
        if(empty($ids))
        {
            $msg = "1|标签项不能为空";
        }
        else
        {
            $Tags = $this->model('Pipi');
            if($Tags->deleteRow($ids))
                $msg = "0|删除成功";
            else
                $msg = "1|数据删除失败";
        }
        echo json_encode($msg);
    }
    
    public function tjdeltagsAction($id) {
        
        $msg = "1|删除失败";
        if(empty($id[0]))
        {
            $msg = "1|编号不能为空";
        }
        else
        {
            $Tags = $this->model('Pipitj');
            if($Tags->deleteRow($id[0]))
                $msg = "0|删除成功";
            else
                $msg = "1|请检编号是否存在";
        }
        echo json_encode($msg);
    }
        //批量删除
    public function tjbatdeltagsAction() {
        $msg = "1|错误";
        $ids = !empty($_POST['ids'])?mysql_escape_string(trim($_POST['ids'])):"";
        if(empty($ids))
        {
            $msg = "1|标签项不能为空";
        }
        else
        {
            $Tags = $this->model('Pipitj');
            if($Tags->deleteRow($ids))
                $msg = "0|删除成功";
            else
                $msg = "1|数据删除失败";
        }
        echo json_encode($msg);
    }
}

?>