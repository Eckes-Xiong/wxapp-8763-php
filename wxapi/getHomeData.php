<?php
require '../admin_entry.php';
//$c->model->checkWxToken(getallheaders());
//获取首页数据
$c = new WxDictController();
$c->getHomeData();