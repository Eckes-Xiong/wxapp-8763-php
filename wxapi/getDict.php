<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
$c = new WxDictController();
$pid=$_GET['pid'];
if($pid){
  $c->toGetDict($pid);
}