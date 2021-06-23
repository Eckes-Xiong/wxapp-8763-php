<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//加入购物车
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);

//合并 购物车的产品
$has = $c->model->getsqlOne("SELECT id,pnumber from wxapp_order WHERE status=0 AND buyerOpenid='{$_SESSION['openid']}' AND pid='{$json['pid']}' AND size='{$json['size']}' AND pname='{$json['pname']}' AND color='{$json['color']}' AND pprice='{$json['pprice']}'");

if($has){
  $c->model->exec("UPDATE wxapp_order SET pnumber=pnumber+{$json['pnumber']} WHERE id = '".$has["id"]."'");

  $data = $c->model->getsqlOne("SELECT COUNT(1) FROM wxapp_order WHERE status=0 AND buyerOpenid='{$_SESSION['openid']}' AND sid='{$json['sid']}'");
  $c->model->getJsonData(1,'添加成功！',array(
    "total"=>$data["COUNT(1)"]
  ));

  exit;
}

//购物车里没有本条商品
$sql = "INSERT INTO wxapp_order (
  size, pid, status, sid, 
  pprice, pname,
  pcover, pnumber, 
  remark, buyerOpenid, color
  ) VALUES (
  '".$json['size']."', '".$json['pid']."', 0, '".$json['sid']."',
  '".$json['pprice']."', '".$json['pname']."',
  '".$json['pcover']."', '".$json['pnumber']."', 
  '".$json['remark']."', '".$_SESSION['openid']."', '".$json['color']."'
)";

$c->model->exec($sql);
//insert
if($c->model->insertId != null && $c->model->insertId != 0){
  $data = $c->model->getsqlOne("SELECT COUNT(1) FROM wxapp_order WHERE status=0 AND buyerOpenid='{$_SESSION['openid']}' AND sid='{$json['sid']}'");
  $c->model->getJsonData(1,'添加成功！',array(
    "total"=>$data["COUNT(1)"]
  ));
}

