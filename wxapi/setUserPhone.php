<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//设置user手机号
$c = new WxDictController();
$c->model->exec("UPDATE wxapp_user SET phone=".$_GET["phone"]." WHERE openid = '".$_SESSION["openid"]."'");

//刷新常驻任务
$resident = $c->model->getsqlOne('SELECT resident FROM wxapp_task_user where openid="'.$_SESSION["openid"].'"');
$od = explode(",", $resident["resident"]);
if($od[1]<100){
  $od[1] = 100;
  $c->model->exec('UPDATE wxapp_task_user SET resident="'.(implode(",",$od)).'" WHERE openid="'.$_SESSION['openid'].'"');
}
//成就
$c->model->setAchieveUser(17);
$c->model->getJsonData(1,'更新成功');