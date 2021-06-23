<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取全部优惠券
$c = new WxDictController();
$json =json_decode(file_get_contents("php://input"),true);

$sql = "SELECT out_trade_no,id,createTime,oids,status,_amount,amount FROM wxapp_order_cart_cache WHERE openid='{$_SESSION['openid']}'";
if($json["status"]){
  $sql = $sql." AND status=".$json["status"];
}else{
  $sql = $sql." AND status>100 AND status<199";
}
$data = $c->model->getSqlAll($sql.' ORDER BY createTime DESC');
$c->model->getJsonData(1,'success',$data);