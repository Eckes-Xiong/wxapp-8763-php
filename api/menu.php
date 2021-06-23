<?php
require '../admin_entry.php';
$app->isLogin();

$c = new QueryController('top_menu');
$c->getFieldAll('privite',$_SESSION['u_privite'],'>=');