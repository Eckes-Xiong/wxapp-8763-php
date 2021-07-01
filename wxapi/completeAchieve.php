<?php
require '../admin_entry.php';
//完成 user 成就
$json =json_decode(file_get_contents("php://input"),true);
$c = new LoginController();
$c->model->checkWxToken(getallheaders());
$id=$_GET["id"];
$it = $c->model->getsqlOne("SELECT integral,title from wxapp_achieve WHERE id='{$id}'");
$c->model->exec("UPDATE wxapp_achieve_user SET status=2 WHERE aid='{$id}' AND openid='{$_SESSION['openid']}'");
$c->model->exec("UPDATE wxapp_user SET integralNum=integralNum+{$it['integral']} WHERE openid='{$_SESSION['openid']}'");
$c->model->writeIntegralLog("+".$it['integral'], "成就：".$it["title"]);
$c->model->getJsonData(1,'success');