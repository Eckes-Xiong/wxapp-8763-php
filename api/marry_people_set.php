<?php
require '../admin_entry.php';

$c = new ReleaseController("marry_yk_people");
$c->marry_yk_people(file_get_contents('php://input'));