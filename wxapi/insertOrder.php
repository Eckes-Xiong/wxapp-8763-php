<?php
require '../admin_entry.php';

// //提交订单
$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);
$osn = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
$sql = "INSERT INTO wxapp_order (
  size, pid, sid, 
  pprice, pname,
  pcover, orderNum, 
  pcode, pnumber, 
  remark, buyerOpenid
  ) VALUES (
  '".$json['size']."', '".$json['pid']."', '".$json['sid']."',
  '".$json['pprice']."', '".$json['pname']."',
  '".$json['pcover']."', '".$osn."',
  '".$json['pcode']."', '".$json['pnumber']."', 
  '".$json['remark']."', '".$_SESSION['openid']."'
)";

//订单数+1
$c->model->exec('UPDATE wxapp_user SET orderNum=orderNum+1 WHERE openid="'.$_SESSION['openid'].'"');

$c->handleInsert($sql);