<?php
require '../admin_entry.php';

//审核店铺
$c = new WxDictController('wxapp_store');$c->model->checkWxToken(getallheaders());
$c->setStore($_GET['status'],$_GET['o']);