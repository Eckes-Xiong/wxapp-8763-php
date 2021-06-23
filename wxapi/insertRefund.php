<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
// 申请退款1
$c = new WxDictController();
$json =json_decode(file_get_contents("php://input"),true);
$osn = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
$sql = "INSERT INTO wxapp_order_refund (
  out_trade_no
  ) VALUES (
  '".$json['out_trade_no']."'
)";
// 107申请退款，108退款完成
$c->model->exec("UPDATE wxapp_order_cart_cache SET status=107 WHERE out_trade_no='{$json['out_trade_no']}'");

$c->handleInsert($sql);