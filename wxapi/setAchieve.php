<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//设置 user 成就
$json =json_decode(file_get_contents("php://input"),true);
$c = new LoginController();

$c->model->setAchieveUser($_GET["id"]);
$c->model->getJsonData(1,'success');