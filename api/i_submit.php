<?php
require '../admin_entry.php';
$app->isLogin();

$c = new ReleaseController();
$c->getNewExec(file_get_contents('php://input'));