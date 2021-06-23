<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//设置 user 每日积分任务
$json =json_decode(file_get_contents("php://input"),true);
$c = new WxDictController();

$c->model->exec('UPDATE wxapp_task_user SET vip="200,0,0,0,0,0" WHERE openid="'.$_SESSION['openid'].'"');
$c->model->exec('UPDATE wxapp_user SET integralNum=integralNum+188 WHERE openid="'.$_SESSION['openid'].'"');
$c->model->writeIntegralLog("+188","vip每日积分任务");
$c->model->getJsonData(1,'更新成功！');