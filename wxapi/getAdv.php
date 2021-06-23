<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
$c = new WxDictController('wxapp_adv');
$c->toGetAdv();