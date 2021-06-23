<?php
require '../admin_entry.php';
$c = new LoginController();
$c->toSignIn($GLOBALS['HTTP_RAW_POST_DATA']);