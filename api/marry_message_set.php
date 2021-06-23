<?php
require '../admin_entry.php';

$c = new ReleaseController("marry_yk_guest");
$c->setMarry_yk_message(file_get_contents('php://input'));