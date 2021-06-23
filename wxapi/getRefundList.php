<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取全部退款订单
$c = new WxDictController();
$sql = 'SELECT * FROM wxapp_order_refund';
$where = '';

$status = $_GET["status"];
if($status!=""){
  $where = ' WHERE status="'.$status.'"';
}

$data = $c->model->getSqlAll($sql.$where.' ORDER BY createTime DESC');

$c->model->getJsonData(1,'success',$data);