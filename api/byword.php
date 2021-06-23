<?php
require '../admin_entry.php';
$c = new QueryController('top_byword');
$id=$_GET['id'];
$c->toQueryOneRandom();