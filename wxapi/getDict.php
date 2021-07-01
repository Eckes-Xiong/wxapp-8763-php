<?php
require '../admin_entry.php';

$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$pid=$_GET['pid'];
if($pid){
  $c->toGetDict($pid);
}