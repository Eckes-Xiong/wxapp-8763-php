<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//设置 user 每日积分任务
$json =json_decode(file_get_contents("php://input"),true);
$c = new WxDictController();

$data = $c->model->getsqlOne('SELECT weekly FROM wxapp_task_user where openid="'.$_SESSION["openid"].'"');
$ow = explode(",",$data["weekly"]);
$ow[$json["idx"]] = 200;
$c->model->exec('UPDATE wxapp_task_user SET weekly="'.(implode(",",$ow)).'" WHERE openid="'.$_SESSION['openid'].'"');
//当前任务的积分和经验
$task = $c->model->getsqlOne("SELECT name, rewards, exp FROM wxapp_task where id='{$json["tid"]}'");

$c->model->writeIntegralLog("+{$task['rewards']}",$task['name']);
$c->model->exec("UPDATE wxapp_user SET integralNum=integralNum+'{$task['rewards']}', expNum=expNum+'{$task['exp']}' WHERE openid='{$_SESSION['openid']}'");
$c->model->getJsonData(1,'更新成功！');