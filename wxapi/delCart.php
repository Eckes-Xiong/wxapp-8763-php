<?php
require '../admin_entry.php';

//删除购物车
$c = new WxDictController();$c->model->checkWxToken(getallheaders());
if($_GET['id']){

  //领取
  $sql = "DELETE FROM wxapp_order WHERE buyerOpenid='".$_SESSION["openid"]."' AND id= ".$_GET['id'];
  $c->model->exec($sql);
  $c->model->getJsonData(1,'删除成功！');
}