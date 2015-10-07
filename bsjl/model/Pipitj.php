<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  Link class file.
 *
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class Pipitj extends Model{
    public $table = "pipitj";
    //更新一行
    public function updateRow($id,$arr)
    {
        $setsql = "";
        foreach ($arr as $key => $value) {
            if($value!="")
                $setsql = $setsql.$key."='".$value."',";
        }
        $setsql = rtrim($setsql,",");
        $sql = "UPDATE ".$this->table." SET ".$setsql." WHERE id = ".$id;
        return $this->db->query ($sql);
    }
    
    //插入一行数据
    public function insertRow($arr)
    {
        $getsql = "";
        $setsql = "";
        foreach ($arr as $key => $value) {
            if($value!="")
            {
                $setsql = $setsql.$key.",";
                $getsql = $getsql."'".$value."',";
            }
        }
        $setsql = rtrim($setsql,",");
        $getsql = rtrim($getsql,",");
        $sql = "INSERT INTO ".$this->table." (".$setsql.") VALUES (".$getsql.")";
        return $this->db->query($sql);
    }
    
    //删除一行数据
    public function deleteRow($id)
    {
        $sql = "DELETE FROM ".$this->table." WHERE id in(".$id.")";
        return $this->db->query($sql);
    }
    
    //获取一行数据
    public function getRow($id)
    {
        $sql = "SELECT * FROM ".$this->table." WHERE tel = '".$id."'";
        return $this->db->get_row ($sql,"ARRAY_A");
    }
    //获取
    public function getAll($limit=0,$offect=0,$condition="")
    {
        $sql = "select * from ".$this->table." where 1 {$condition} order by id desc limit ".$limit.",".$offect;
        return $this->db->get_results($sql,"ARRAY_A");
    }
    
        //获取
    public function getAllno($condition)
    {
        $sql = "select * from ".$this->table." where 1 {$condition}";
        return $this->db->get_results($sql,"ARRAY_A");
    }
    //计算总数
    public function count($condition="")
    {
        $sql = "select count(*) from ".$this->table." where 1 {$condition}";
        return $this->db->get_var($sql);
    }
}

?>