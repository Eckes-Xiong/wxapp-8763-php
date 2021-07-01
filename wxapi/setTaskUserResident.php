<?php
require '../admin_entry.php';

//设置 user 常驻任务
$json =json_decode(file_get_contents("php://input"),true);
$c = new WxDictController();$c->model->checkWxToken(getallheaders());

$data = $c->model->getsqlOne('SELECT resident FROM wxapp_task_user where openid="'.$_SESSION["openid"].'"');

$idx = $json["idx"];
//从100->200
$od = explode(",", $data["resident"]);
$od[$idx] = 200;
$c->model->exec('UPDATE wxapp_task_user SET resident="'.(implode(",",$od)).'" WHERE openid="'.$_SESSION['openid'].'"');

//当前任务的积分和经验
$task = $c->model->getsqlOne('SELECT name,rewards, exp FROM wxapp_task where id="'.$json["tid"].'"');
//用户+积分和经验
$c->model->exec('UPDATE wxapp_user SET integralNum=integralNum+'.$task['rewards'].', expNum=expNum+'.$task['exp'].' WHERE openid="'.$_SESSION['openid'].'"');
$c->model->writeIntegralLog("+{$task['rewards']}",$task['name']);
$c->model->getJsonData(1,'更新成功！');
exit;