<?php
require '../admin_entry.php';

//获取全部徽章
$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$sql = 'SELECT * FROM wxapp_badge';

$data = $c->model->getSqlAll($sql);
$c->model->getJsonData(1,'success',$data);