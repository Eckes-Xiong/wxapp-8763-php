<?php
require '../admin_entry.php';
//$c->model->checkWxToken(getallheaders());
//获取全部优惠券
$c = new WxDictController();
$sql = 'SELECT * FROM wxapp_coupon';
$where =  '';
$status = $_GET['status'];

if($status==1){
  $where = ' WHERE endTime>"'.date("Y-m-d H:i:s").'" AND status="'.$status.'" ORDER BY createTime DESC';
}else{
  $where = ' WHERE status="'.$_GET['status'].'" ORDER BY createTime DESC';
}


$data = $c->model->getSqlAll($sql.$where);
$len = count($data);
for($i=0; $i<$len; $i++){
  $sql_ = 'SELECT COUNT(1) FROM wxapp_user_coupon WHERE cid="'.$data[$i]["id"].'" AND openid="'.$_SESSION['openid'].'"';
  $data_ = $c->model->getsqlOne($sql_);
  
  if($data_["COUNT(1)"]!=0){
    $data[$i]["isUserHas"]=true;
  }
}

$c->model->getJsonData(1,'success',$data);