<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取用户积分log
$c = new WxDictController();
$json =json_decode(file_get_contents("php://input"),true);
$sql = "SELECT id, integral, remark, createTime FROM wxapp_log_integral WHERE '{$json["start"]}'<=createTime AND createTime<='{$json["end"]}' AND status=1 AND openid='{$_SESSION['openid']}' ORDER BY createTime DESC";
$data = $c->model->getSqlAll($sql);
$c->model->getJsonData(1,'success',$data);