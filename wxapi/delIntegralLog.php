<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//删除用户积分log
$c = new WxDictController();
$sql = "UPDATE wxapp_log_integral SET status=2 WHERE id={$_GET['id']}";
$c->model->exec($sql);
$c->model->getJsonData(1,'success');