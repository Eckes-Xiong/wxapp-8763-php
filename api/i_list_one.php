<?php
require '../admin_entry.php';
$c = new QueryController('top_infomation_list');
if($_GET['title']){
  $c->toQueryOne($_GET['title'],'list_title');
}
