<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//购买积分 微信支付第一步：统一下单
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);
$json["osn"]="wxpayI";
$c->wxPay($json);