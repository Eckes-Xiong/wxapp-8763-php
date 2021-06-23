<?php
require '../admin_entry.php';

$c = new QueryController('top_infomation_list');
$c->getCountEveryDay();