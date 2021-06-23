<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取user积分任务
$c = new LoginController();
$data = $c->model->refreshDailyTask(true);
$c->model->getJsonData(1,'success',$data);