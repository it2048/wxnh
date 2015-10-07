<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  systemController class file.
 *
 * @author 熊方磊 <xiongfanglei@kingsoft.com>
 */
class systemController extends Pt {

    //上传文件到upload
    public function upldAction() {
        $tempPath = 'public/upload/' . date("YmdHis") . mt_rand(10000, 99999) . '.tmp';
        $localName = "";
        $inputName = "filedata";
        $upExt='rar,zip,jpg,jpeg,gif,png,swf';//上传扩展名
        $err = "";
        $msg = "";

        $upfile = @$_FILES[$inputName];
        if (!isset($upfile))
            $err = '文件域的name错误';
        elseif (!empty($upfile['error'])) {
            switch ($upfile['error']) {
                case '1':
                    $err = '文件大小超过了php.ini定义的upload_max_filesize值';
                    break;
                case '2':
                    $err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
                    break;
                case '3':
                    $err = '文件上传不完全';
                    break;
                case '4':
                    $err = '无文件上传';
                    break;
                case '6':
                    $err = '缺少临时文件夹';
                    break;
                case '7':
                    $err = '写文件失败';
                    break;
                case '8':
                    $err = '上传被其它扩展中断';
                    break;
                case '999':
                default:
                    $err = '无有效错误代码';
            }
        } elseif (empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')
            $err = '无文件上传';
        else {
            move_uploaded_file($upfile['tmp_name'], $tempPath);
            $localName = $upfile['name'];
            $fileInfo = pathinfo($localName);
            $extension = $fileInfo['extension'];
            if (preg_match('/^(' . str_replace(',', '|', $upExt) . ')$/i', $extension)) {
                $bytes = $upfile['size'];
                if ($bytes > 2097152||$bytes<=0)
                    $err = '上传文件不能大于2M';
                else {
                    $service = $this->getImg(); //存储文件到牛逼目录
                    $bucket = 'it2048';
                    $key = date("YmdHis").mt_rand(1000,9999).".".$extension;
                    try{
                        //存储文件到牛逼目录
                        $service->put_object($bucket,$key,$tempPath);
                    } catch (Exception $e) {
                    }
                    $newFilename="public/upload/".$key;
                    rename($tempPath,$newFilename);
                    @chmod($newFilename,0755);
                    $newFilename = Application::$_config['home']['url']."/".$newFilename;
                    $Appendix = $this->model('Appendix');
                    $id = $Appendix->insertRow($newFilename,$bytes,$extension);
                    $msg="{'url':'".$newFilename."','localname':'".$this->jsonString($localName)."','id':".$id."}";
                }

            } 
            else $err='上传文件扩展名必需为：'.$upExt;
            @unlink($tempPath);
        }
        echo "{'err':'".$this->jsonString($err)."','msg':".$msg."}";
       
    }
    private function jsonString($str)
    {
        return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
    }

}

?>