<?php
require '../admin_entry.php';

$c = new WxDictController('wxapp_adv');$c->model->checkWxToken(getallheaders());
$c->toGetAdv();