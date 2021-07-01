<?php
require '../admin_entry.php';

//设置 user 每日积分任务
$json =json_decode(file_get_contents("php://input"),true);
$c = new LoginController();$c->model->checkWxToken(getallheaders());

$data = $c->model->getsqlOne('SELECT daily,weekly FROM wxapp_task_user where openid="'.$_SESSION["openid"].'"');

//从100->200
$idx = $json["idx"];
if($idx){
  $od = explode(",", $data["daily"]);
  $od[$idx] = 200;
  $c->model->exec('UPDATE wxapp_task_user SET daily="'.(implode(",",$od)).'" WHERE openid="'.$_SESSION['openid'].'"');
  
  //当前任务的积分和经验
  $task = $c->model->getsqlOne('SELECT name, rewards, exp FROM wxapp_task where id="'.$json["tid"].'"');
  //用户+积分和经验
  $c->model->writeIntegralLog("+{$task['rewards']}",$task['name']);
  $c->model->exec('UPDATE wxapp_user SET integralNum=integralNum+'.$task['rewards'].', expNum=expNum+'.$task['exp'].' WHERE openid="'.$_SESSION['openid'].'"');
  $c->model->getJsonData(1,'更新成功！');
  exit;
}

//->100
$idx = $json["idx_"];
if($idx){
  $od = explode(",", $data["daily"]);
  $od[$idx] = 100;
  $c->model->exec('UPDATE wxapp_task_user SET daily="'.(implode(",",$od)).'" WHERE openid="'.$_SESSION['openid'].'"');
  $c->model->getJsonData(1,'更新成功！');
  exit;
}

//签到
$set = array();

if($json["od"] && $data["daily"]==$json["od"]){
  array_push($set,'daily="'.$json["nd"].'"');
}

if($json["ow"] && $data["weekly"]==$json["ow"]){
  array_push($set,'weekly="'.$json["nw"].'"');
}

$c->model->exec('UPDATE wxapp_task_user SET '.(implode(",",$set)).' WHERE openid="'.$_SESSION['openid'].'"');

//当前任务的积分和经验
$task = $c->model->getsqlOne('SELECT name, rewards, exp FROM wxapp_task where id="'.$json["tid"].'"');
//用户+积分和经验
$c->model->exec('UPDATE wxapp_user SET integralNum=integralNum+'.$task['rewards'].', expNum=expNum+'.$task['exp'].' WHERE openid="'.$_SESSION['openid'].'"');
$c->model->writeIntegralLog("+{$task['rewards']}",$task['name']);
$c->model->getJsonData(1,'更新成功！');