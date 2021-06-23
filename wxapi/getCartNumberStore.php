<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取当前店铺当前用户的购物车列表
$c = new WxDictController();

$sql = 'SELECT id,pid,pnumber FROM wxapp_order WHERE status=0 AND buyerOpenid="'.$_SESSION["openid"].'" AND sid="'.$_GET["sid"].'"';
$data = $c->model->getsqlAll($sql);
$c->model->getJsonData(1,'success',array(
  "total"=>count($data),
  "data"=>$data
));