<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取全部徽章
$c = new WxDictController();
$sql = 'SELECT * FROM wxapp_badge';

$data = $c->model->getSqlAll($sql);
$c->model->getJsonData(1,'success',$data);