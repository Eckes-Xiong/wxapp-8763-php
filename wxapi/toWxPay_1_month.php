<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//月卡 微信支付第一步：统一下单
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);
$json["osn"]="wxpayM";
$c->wxPay($json);