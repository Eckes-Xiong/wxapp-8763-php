<?php
require '../admin_entry.php';
 
class Response{
 public static function json($code,$message="",$data=array()){
  $result=array(
   'code'=>$code,
   'message'=>$message,
   'data'=>$data
  );
  //输出json
  echo json_encode($result);
  exit;
 }
}
 
 
$date = date("Ymd");
$time = date("Hms").mt_rand(0,9999999);
$targetFolder = '/uploads/'.$date; // 
$dir = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;

function  Directory( $dir ){
  return   is_dir ( $dir )  or  (Directory(dirname( $dir ))  and   mkdir ( $dir , 0777));
}
//建立目录
Directory($dir);

$tempFile=$_FILES['file']['tmp_name'];
//上传文件类型列表
$uptypes=array(
 'image/jpg',
 'image/jpeg',
 'image/png',
 'image/pjpeg',
 'image/gif',
 'image/bmp',
 'image/x-png'
);
//如果当前图片不为空
if(!empty($tempFile)){

  $fileParts = pathinfo($_FILES['file']['name']);
  $targetFile = rtrim($dir,'/') . '/' . $time .".". $fileParts['extension'];

  move_uploaded_file($tempFile,$targetFile);
  $src = rtrim($targetFolder,'/'). '/' . $time .".". $fileParts['extension'];
  Response::json(1,'success',$src);

}//endif
Response::json(0,'error',$tempFile);