<?php
require '../admin_entry.php';

//获取用户的购物车
$c = new LoginController();$c->model->checkWxToken(getallheaders());
$c->isWxPayOrderSuccess();
$integral = $c->model->getSqlOne("SELECT integralNum FROM wxapp_user WHERE openid='{$_SESSION["openid"]}'");

$data = $c->model->getSqlAll("SELECT distinct sid FROM wxapp_order WHERE status=0 AND buyerOpenid='{$_SESSION["openid"]}'");
$len = count($data);

$arr = array();
$total = 0;

for($i=0; $i<$len; $i++){
  $store = $c->model->getsqlOne("SELECT isOnShop,name,address FROM wxapp_store WHERE id='{$data[$i]["sid"]}'");
  $carts = $c->model->getSqlAll("SELECT id,pname,pnumber,pcover,createTime,color,size,remark,pprice FROM wxapp_order WHERE sid='{$data[$i]["sid"]}' AND status=0 AND buyerOpenid='{$_SESSION["openid"]}'");
  array_push($arr, array(
    "name"=>$store["name"],
    "address"=>$store["address"],
    "id"=>$data[$i]["sid"],
    "isOnShop"=>$store["isOnShop"],
    "checked"=>true,
    "count"=>count($carts),
    "order"=>$carts
  ));
  $total = $total+count($carts);
}

$c->model->getJsonData(1,$integral["integralNum"],$arr,$total);