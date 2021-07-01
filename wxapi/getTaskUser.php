<?php
require '../admin_entry.php';

//获取user积分任务
$c = new LoginController();$c->model->checkWxToken(getallheaders());
$data = $c->model->refreshDailyTask(true);
$c->model->getJsonData(1,'success',$data);