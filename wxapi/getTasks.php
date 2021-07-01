<?php
require '../admin_entry.php';

//获取积分任务
$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$data = $c->model->getSqlAll('SELECT * FROM wxapp_task WHERE status=1 ORDER BY type ASC,id ASC');
$c->model->getJsonData(1,'success',$data);