<?php
require '../admin_entry.php';
//$app->checkWxToken(getallheaders());
//get one order
$c = new WxDictController();
$sql = 'SELECT pid,pname,pcover,pprice,pnumber,createTime,size,orderNum,pcode,endTime,status,remark,isUrgent,id,vipSort FROM wxapp_order ';
if($_GET['id']==0){
  $where = 'WHERE buyerOpenid="'.$_SESSION['openid'].'" ORDER BY createTime DESC LIMIT 1';
}else{
  $where =  'WHERE id="'.$_GET['id'].'"';
}
$c->handleSearchOne($sql.$where);