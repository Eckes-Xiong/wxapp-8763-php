<?php
require '../admin_entry.php';
//$c->model->checkWxToken(getallheaders());
//领取优惠券
$c = new WxDictController();
if($_GET['id']){

  //判断是否领过
  $sql = 'SELECT COUNT(1) FROM wxapp_user_coupon WHERE openid="'.$_SESSION['openid'].'"';
  $data = $c->model->getsqlOne($sql);
  if($data["COUNT(1)"]!=0){
    $c->model->getJsonData(0,'您已领取过此优惠券！');
    exit;
  }

  //判断是否已领完
  $sql = 'SELECT quantity,endTime FROM wxapp_coupon WHERE id="'.$_GET['id'].'"';
  $data = $c->model->getsqlOne($sql);
  if($data["quantity"]<=0){
    $c->model->getJsonData(0,'剩余券数不足！');
    exit;
  }

  //领取
  $sql = "UPDATE wxapp_coupon SET quantity='".($data["quantity"]-1)."' WHERE id='".$_GET['id']."'";
  $c->model->exec($sql);

  $sql = "INSERT INTO wxapp_user_coupon (cid, openid, endTime) VALUES ('".$_GET['id']."', '".$_SESSION['openid']."', '".$data["endTime"]."')";
  $c->model->exec($sql);
  if($c->model->insertId != null && $c->model->insertId != 0){
    $c->model->getJsonData(1,'领取成功！');
  }
}