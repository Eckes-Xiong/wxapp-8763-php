<?php
require '../admin_entry.php';

//积分储值 支付成功
$c = new LoginController();$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);
$c->isWxPaySuccess($json['out_trade_no'],3);
$c->model->AfterWxPay3($json);
$c->model->getJsonData(1,'success');