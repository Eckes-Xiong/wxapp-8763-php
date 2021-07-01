<?php
require '../admin_entry.php';

$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);
$code = "CP" . date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
$sql = "INSERT INTO wxapp_coupon (
  ptype, amount, quantity, code,
  moneyoff, status, pid, pcover, price,
  endTime, startTime
  ) VALUES (
    '{$json["ptype"]}', '{$json["amount"]}', '{$json["amount"]}', '{$code}',
    '{$json["moneyoff"]}', '{$json["status"]}', '{$json["pid"]}',
    '{$json["pcover"]}', '{$json["price"]}',
    '{$json["endTime"]}', '{$json["startTime"]}'
  )";

$c->handleInsert($sql);