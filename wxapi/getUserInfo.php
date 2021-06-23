<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
$c = new LoginController("wxapp_user");
$json =json_decode(file_get_contents("php://input"),true);

$c->isWxPayOrderSuccess(2);
$c->isWxPayOrderSuccess(3);

$c->toGetWxUserInfo($json);