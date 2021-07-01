<?php
require '../admin_entry.php';

//获取自己的购物车数量
$c = new WxDictController();
$c->model->checkWxToken(getallheaders());
$sql = "SELECT COUNT(1) FROM wxapp_order WHERE status=0 AND buyerOpenid='{$_SESSION['openid']}'";
$data = $c->model->getsqlOne($sql);
$c->model->getJsonData(1,'success',array(
  "total"=>$data["COUNT(1)"]
));