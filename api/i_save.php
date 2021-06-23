<?php
require '../admin_entry.php';
$app->isLogin();

$c = new ReleaseController();
$c->getUpdateExec(file_get_contents('php://input'));