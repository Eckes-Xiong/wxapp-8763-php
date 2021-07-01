<?php
require '../admin_entry.php';
$c = new LoginController("wxapp_user");
$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);

$c->isWxPayOrderSuccess(2);
$c->isWxPayOrderSuccess(3);

$c->toGetWxUserInfo($json);