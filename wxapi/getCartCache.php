<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取购物车数量
$c = new WxDictController();

$data = $c->model->getsqlOne('SELECT out_trade_no FROM wxapp_order_cart_cache WHERE openid="'.$_SESSION["openid"].'"');
$c->model->getJsonData(1,'success',$data);