<?php
require '../admin_entry.php';
//$app->checkWxToken(getallheaders());
$c = new WxDictController('wxapp_store');
$sql = "SELECT bg,cover,id,level,name,scope,sort,status,statusDesc,subtitle,typeName FROM wxapp_store WHERE status='{$_GET['status']}'";
if($_GET["plf"]!="release"){
  $data = $c->model->getSqlAll($sql." ORDER BY sort DESC");
  $c->model->getJsonData(1,'',$data);
  exit;
}
$data = $c->model->getSqlAll($sql." AND id=1 ORDER BY sort DESC");
$c->model->getJsonData(1,'',$data);