<?php
require '../admin_entry.php';

//获取全部退款订单
$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$sql = 'SELECT * FROM wxapp_order_refund';
$where = '';

$status = $_GET["status"];
if($status!=""){
  $where = ' WHERE status="'.$status.'"';
}

$data = $c->model->getSqlAll($sql.$where.' ORDER BY createTime DESC');

$c->model->getJsonData(1,'success',$data);