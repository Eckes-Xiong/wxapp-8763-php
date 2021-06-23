<?php
require '../admin_entry.php';
print_r($_SESSION['openid']);
$c = new WxController();
$c->postSubscribeMsg($GLOBALS['HTTP_RAW_POST_DATA']);