<?php
require '../admin_entry.php';

//微信支付第一步：统一下单
$c = new LoginController();$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);
$json["osn"]="wxpay";
$c->wxPay($json);