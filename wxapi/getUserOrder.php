<?php
require '../admin_entry.php';

//获取全部优惠券
$c = new LoginController();$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);


$sql = "SELECT out_trade_no,id,createTime,oids,status,_amount,amount FROM wxapp_order_cart_cache WHERE openid='{$_SESSION['openid']}'";
if($json["status"]){
  $sql = $sql." AND status=".$json["status"];
}else{
  $c->isWxPayOrderSuccess();
  $sql = $sql." AND status>100 AND status<199";
}
$data = $c->model->getSqlAll($sql.' ORDER BY createTime DESC');
$len = count($data);
for ($i=0; $i < $len; $i++) {
  $m = explode(",", $data[$i]["oids"]);
  $product= $c->model->getSqlOne("SELECT pname, pcover FROM wxapp_order WHERE id='{$m[0]}'");
  $data[$i]["pname"] = $product["pname"];
  $data[$i]["pcover"] = $product["pcover"];
  $data[$i]["total"] = count($m);
}

$c->model->getJsonData(1,'success',$data);