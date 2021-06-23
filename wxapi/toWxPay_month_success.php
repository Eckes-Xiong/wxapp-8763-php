<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//微信支付 月卡 支付成功
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);
$c->isWxPaySuccess($json['out_trade_no'],2);
$c->model->AfterWxPay2($json);
$c->model->getJsonData(1,'success');