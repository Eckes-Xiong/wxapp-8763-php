<?php
require '../admin_entry.php';

$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$user = $c->model->getsqlAll("SELECT hid,point FROM wxapp_h5game_user WHERE openid='{$_SESSION['openid']}'");
$data = $c->model->getsqlAll("SELECT * FROM wxapp_h5game_list WHERE status=1 ORDER BY play DESC");
$len1 = count($user);
$len2 = count($data);
for ($i=0; $i < $len1; $i++) {
  for ($m=0; $m < $len2; $m++) {
    if($user[$i]["hid"] == $data[$m]["id"]){
      $data[$m]["point"] = $user[$i]["point"];
      break;
    }
  }
}
$c->model->getJsonData(1,'success',$data);