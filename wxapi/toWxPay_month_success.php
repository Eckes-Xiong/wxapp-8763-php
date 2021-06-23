<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//微信支付 月卡 支付成功
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);


$c->isWxPaySuccess($json['out_trade_no'],2);
$c->model->exec("UPDATE wxapp_order_cart_cache SET status='202' WHERE status='2' AND out_trade_no='{$json['out_trade_no']}'");

$me = $c->model->getsqlOne("SELECT isVip, levelTime FROM wxapp_user where openid='{$_SESSION['openid']}'");
if($me["isVip"]==1){
  $_time = date("Y-m-d H:i:s", strtotime($me['levelTime'])+24*60*60*30);
}else{
  $_time = date("Y-m-d H:i:s", time()+24*60*60*30);
}

$c->model->exec("INSERT INTO wxapp_order_vip ( status,openid, out_trade_no) VALUES (776, '".$_SESSION['openid']."', '".$json['out_trade_no']."')");
$c->model->exec('UPDATE wxapp_user SET isVip=1, levelTime="'.$_time.'" WHERE openid="'.$_SESSION['openid'].'"');
$c->model->exec("INSERT INTO wxapp_user_coupon (cid, openid, endTime) VALUES ('2', '".$_SESSION['openid']."', '".$_time."')");

$c->model->writePayLog("-{$json['amount']}","购买满月祝福");
$c->model->getJsonData(1,'success');