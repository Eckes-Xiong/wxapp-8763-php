<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//设置购物车商品的数量
$c = new WxDictController();
$sql_ = "UPDATE wxapp_order SET pnumber={$_GET["pnumber"]} WHERE id='{$_GET["id"]}'";
$c->model->exec($sql_);
$c->model->getJsonData(1,'修改成功');