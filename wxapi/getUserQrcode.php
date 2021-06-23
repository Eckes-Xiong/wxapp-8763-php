<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取用户的小程序码
$c = new LoginController();
$access_token = $c->getAccessToken();

$url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token='.$access_token;
$data = array();
$data['path'] = "pages/tabbar/my/my?userid=".$_GET["id"];
$data['auto_color'] = true;
$data['width'] = 300;

$data = json_encode($data);

$header  = array(
  'Content-Type:application/json; charset=UTF-8',
  'Accept:application/json',
  'User-Agent:'."WXPaySDK/3.0.10 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$c->mchid
);
$ret = $c->model->curl_post_https($url,$data,$header);
if(is_null(json_decode($ret,true))){
  $dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/userQrcode/uid-'.$_GET["id"].".jpg";

  $file = fopen($dir,"w") or die("cant open file");
  fwrite($file,"$ret");
  fclose($file);

  $c->model->exec("UPDATE wxapp_user SET qrcode='/uploads/userQrcode/uid-".$_GET["id"].".jpg' WHERE id = '".$_GET["id"]."'");

  $c->model->getJsonData(1,'success',array(
    "img"=>"/uploads/userQrcode/uid-".$_GET["id"].".jpg"
  ));
  exit;
}else{
  $c->model->getJsonData(0,'请求失败~',$ret);
  exit;
}