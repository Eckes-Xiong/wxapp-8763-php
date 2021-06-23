<?php
require '../admin_entry.php';
//$app->checkWxToken(getallheaders());
// user 每日分享积分任务
$json =json_decode(file_get_contents("php://input"),true);
$c = new WxDictController();

$data = $c->model->getsqlOne('SELECT daily,weekly FROM wxapp_task_user where id="'.$json["id"].'"');

$od = explode(",", $data["daily"]);
$ow = explode(",", $data["weekly"]);

if($od[3]<100){
  $od[3]=100;
  if($ow[3]<100){
    $ow[3] = $ow[3]+20;
  }

  $nd = implode(",", $od);
  $nw = implode(",", $ow);

  $c->model->exec('UPDATE wxapp_task_user SET daily="'.$nd.'", weekly="'.$nw.'" WHERE id="'.$json["id"].'"');
  $c->model->getJsonData(1,'更新成功！');
}else{
  $c->model->getJsonData(102,'分享已完成');
}
