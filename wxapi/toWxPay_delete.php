<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);
$c->model->exec("DELETE FROM wxapp_order_cart_cache WHERE out_trade_no='{$json['out_trade_no']}'");
$c->model->getJsonData(1,'success');