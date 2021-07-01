<?php
require '../admin_entry.php';

//获取用户领取的优惠券
$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$sql = 'SELECT id, cid, endTime FROM wxapp_user_coupon WHERE status="'.$_GET['status'].'" AND openid="'.$_SESSION['openid'].'" ORDER BY useTime DESC';

$data = $c->model->getSqlAll($sql);
$total = count($data);

$result = array();
for ($i=0; $i < $total; $i++) {
  if((strtotime(date("Y-m-d H:i:s")) - strtotime($data[$i]["endTime"]))>0 && $_GET["status"]==1){
    $sql_ = "UPDATE wxapp_user_coupon SET status = 2 WHERE cid = '".$data[$i]["cid"]."'";
    $c->model->exec($sql_);
    continue;
  }else{
    $coupon = $c->model->getSqlOne("SELECT name,price,moneyoff,saleoff,ptype,pcover FROM wxapp_coupon WHERE id='{$data[$i]["cid"]}'");
    $data[$i]["name"] = $coupon["name"];
    $data[$i]["price"] = $coupon["price"];
    $data[$i]["moneyoff"] = $coupon["moneyoff"];
    $data[$i]["saleoff"] = $coupon["saleoff"];
    $data[$i]["ptype"] = $coupon["ptype"];
    $data[$i]["pcover"] = $coupon["pcover"];
    $l = count($result);
    $result[$l] = $data[$i];
  }
}

$c->model->getJsonData(1,'success',$result);