<?php
require '../admin_entry.php';
$c = new LoginController("wxapp_user");
$c->toGetWxToken($GLOBALS['HTTP_RAW_POST_DATA']);