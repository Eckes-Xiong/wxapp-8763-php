<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//积分储值 支付成功
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);

$c->isWxPaySuccess($json['out_trade_no'],3);
$c->model->exec("UPDATE wxapp_order_cart_cache SET status='302' WHERE status='3' AND out_trade_no='{$json['out_trade_no']}'");


$it = $c->model->getsqlOne("SELECT * FROM wxapp_integral WHERE id='{$json['oids']}'");
$integral = $it["point"]+$it["p_add"];

$c->model->exec("INSERT INTO wxapp_order_integral ( openid, out_trade_no ,iid ) VALUES ('{$_SESSION['openid']}', '{$json['out_trade_no']}', '{$json['oids']}')");
$c->model->exec("UPDATE wxapp_user SET integralNum=integralNum+{$integral} WHERE openid='{$_SESSION['openid']}'");

$c->model->writeIntegralLog("+{$it['point']}+{$it['p_add']}","积分储值");
$c->model->getJsonData(1,'success');