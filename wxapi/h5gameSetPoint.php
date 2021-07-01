<?php
require '../admin_entry.php';

$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$id = $_GET["id"];
$point = $_GET["point"];
$count = $c->model->getsqlOne("SELECT id,point FROM wxapp_h5game_user WHERE hid='{$id}' AND openid='{$_SESSION['openid']}'");
if($count){
  if($point>$count["point"]){
    $c->model->exec("UPDATE wxapp_h5game_user SET point='{$point}' WHERE id='{$count['id']}'");
  }
}else{
  $c->model->exec("INSERT INTO wxapp_h5game_user (hid, openid, point) VALUES ('{$id}', '{$_SESSION['openid']}', '{$point}')");
}
$c->model->getJsonData(1,'success');