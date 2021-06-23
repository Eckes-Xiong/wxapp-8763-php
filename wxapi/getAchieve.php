<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取全部成就
$c = new WxDictController();
$sql = 'SELECT id,title,subtitle,integral FROM wxapp_achieve ORDER BY sort DESC';

$data = $c->model->getSqlAll($sql);
$data_user = $c->model->getSqlAll('SELECT aid,status FROM wxapp_achieve_user WHERE openid="'.$_SESSION["openid"].'"');

$c->model->getJsonData(1,'success',array(
  "all"=>$data,
  "user"=>$data_user
));