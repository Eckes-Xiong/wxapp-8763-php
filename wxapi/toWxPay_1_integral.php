<?php
require '../admin_entry.php';

//购买积分 微信支付第一步：统一下单
$c = new LoginController();$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);
$c->isWxPayOrderSuccess(3);
$data = $c->model->getsqlOne("SELECT discount FROM wxapp_integral WHERE id={$json['id']}");
$json["amount"] = (int)$data["discount"];
$json["_amount"] = (int)$data["discount"];
$json["coupon"] = 0;
$json["osn"]="wxpayI";
$json["sid"]=$json['id'];
$c->wxPay($json);