<?php
class ReleaseModel extends Model {

    public function setMarry_yk_Message($fields){

        $sql = "INSERT INTO ".$this->tb."(
            name, tel, message, exp
            ) VALUES (
            '".$fields['name']."', '".$fields['tel']."', '".$fields['message']."',
            '".$fields['exp']."'
        )";

        $this->exec($sql);
        //echo $sql;
    }
    public function setMarry_yk_People($fields){

        $sql = "INSERT INTO ".$this->tb."(name) VALUES ('".$fields->name."')";
        $this->exec($sql);
        //echo $sql;
    }

    //添加数据，修改数据
    public function getExec($fields,$type){
        if($type=='insert'){

            $sql = "INSERT INTO ".$this->tb."(
				list_title, list_subtitle, list_description,
				list_createDateTime, list_cls_First, list_cls_Second,
				list_cls_Third, list_content, list_hotChecked,
				list_yunFile, list_cover, list_user, list_markdown
				) VALUES (
				'".$fields['title']."', '".$fields['subtitle']."',
				'".$fields['description']."', '".$fields['createDateTime']."',
				'".$fields['cls_First']."', '".$fields['cls_Second']."',
				'".$fields['cls_Third']."', '".$fields['value']."',
				'".$fields['hotChecked']."', '".$fields['yunFile']."',
				'".$fields['cover']."', '".$_SESSION['username']."', '".$fields['markdown']."'
				)";

        }else if($type=='update'){
            $sql = "UPDATE ".$this->tb." SET 
                list_title='".$fields['title']."', 
                list_subtitle='".$fields['subtitle']."', 
                list_description='".$fields['description']."',
                list_createDateTime='".$fields['createDateTime']."', 
                list_cls_First='".$fields['cls_First']."', 
                list_cls_Second='".$fields['cls_Second']."',
                list_cls_Third='".$fields['cls_Third']."', 
                list_content='".$fields['value']."',
                list_markdown='".$fields['markdown']."',
                list_hotChecked='".$fields['hotChecked']."',
                list_yunFile='".$fields['yunFile']."', 
                list_cover='".$fields['cover']."', 
                list_user='".$_SESSION['username']."'
				WHERE list_id='".$fields['list_id']."'";
        }

        $this->exec($sql);
        //echo $sql;
    }

    //添加数据之后，创建文件
    public function creatPage($dir){
        /**
         * 创建文件
         * dirname => 文件目录
         * dirfile => 文件名
         * templat => 替换的模版
         **/
        $dirpath = HOME_PATH."/release/".$dir;
        $filename = date("Ymd").$this->insertId.".php";
        $view = LIB_PATH.'/admin/release/'.$this->tb.".php";//release_info.php

        if (!file_exists($dirpath) && !mkdir($dirpath, 0777, true)){ echo "无法创建目录"; exit; }
        else if (!is_writeable($dirpath)) { echo "目录不可写"; exit; }

        //创建文件
        $create = fopen( $dirpath."/".$filename , "w+");

        //打开模版,读取模版内容,替换需要更改的内容
        $temp = fopen($view,"r");
        $content = fread($temp,filesize($view));
        $content = str_replace("{latest_id}",$this->insertId,$content);

        //写入内容到新文件
        if(!fwrite($create,$content)){ echo "无法写入文件"; exit; }
        //更新数据库文件url
        $sql = "update ".$this->tb." set i_url = '/release/".$dir."/".$filename."' where i_id='".$this->insertId."'";
        $this->exec($sql);
    }
    //判断数据为空
    public function isEmpty($data,$type){
		
		$data = preg_replace('/\\\n/','|n',$data);
        $_data = $this->json2Array($data);
		
        if(empty($_data['title'])){
            $this->getJsonData(10301,'title不可以为空');
        }else if(empty($_data['cls_First'])){
            $this->getJsonData(10302,'className不可以为空');
        }else if(empty($_data['value'])){
            $this->getJsonData(10303,'content不可以为空');
        }else{
            $this->getExec($_data,$type);
        }
    }
}