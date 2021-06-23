<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取退款订单详情
$c = new WxDictController();
$data = $c->model->getSqlOne("SELECT _amount, amount, coupon, address, addrType, sid, integral FROM wxapp_order_cart_cache WHERE status=107 AND out_trade_no='{$_GET['out_trade_no']}'");
$store = $c->model->getsqlOne("SELECT name FROM wxapp_store WHERE id='{$data["sid"]}'");
$carts = $c->model->getSqlAll("SELECT id,pname,pnumber,pcover,color,size,remark,pprice FROM wxapp_order WHERE out_trade_no='{$_GET['out_trade_no']}'");
$data["sname"] = $store["name"];
$data["total"] = count($carts);
$data["order"] = $carts;

$c->model->getJsonData(1,'success',$data);