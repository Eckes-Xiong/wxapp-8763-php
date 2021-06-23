<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取退款订单详情
$c = new WxDictController();

$data = $c->model->getSqlAll('SELECT sid FROM wxapp_order WHERE orderNum in('.$_GET["oids"].')');
$len = count($data);

$arr = array();
$total = 0;

for($i=0; $i<$len; $i++){
  $store = $c->model->getsqlOne('SELECT name FROM wxapp_store WHERE id="'.$data[$i]["sid"].'"');
  $carts = $c->model->getSqlAll('SELECT id,pid,sid,pname,pnumber,pcover,createTime,color,size,remark,pprice FROM wxapp_order WHERE status=99');
  array_push($arr, array(
    "name"=>$store["name"],
    "sid"=>$data[$i]["sid"],
    "checked"=>true,
    "count"=>count($carts),
    "order"=>$carts
  ));
  $total = $total+count($carts);
}

$c->model->getJsonData(1,'success',$arr,$total);