<?php
require '../admin_entry.php';
//$app->checkWxToken(getallheaders());
//获取首页数据
$c = new WxDictController();
$c->getHomeData();