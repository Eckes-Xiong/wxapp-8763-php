<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//微信支付：申请退款
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);

$arr__ = $c->refund($json);

if($arr__['status']=="PROCESSING"||$arr__['status']=="SUCCESS"){
  $c->model->exec('UPDATE wxapp_order_refund SET status=1 WHERE out_trade_no="'.$json['no'].'"');
  $c->model->exec('UPDATE wxapp_order_cart_cache SET status=108 WHERE out_trade_no="'.$json['no'].'"');
  //减积分
  $i = intval($json["amount"]/100)*2;
  if($i>0){
    $c->model->exec("UPDATE wxapp_user SET integralNum=integralNum-{$i} WHERE openid='{$_SESSION["openid"]}'");
    $c->model->writeIntegralLog("-{$i}","退款");
  }
  $c->model->writePayLog("+{$json['amount']}","退款");
  $c->model->getJsonData(1,'退款已提交到微信');
}else{
  $c->model->getJsonData(0,'error',$arr__);
}