<?php
require '../admin_entry.php';
$c = new QueryController('top_'.$_GET['tb']);
$id=$_GET['id'];
$key = $_GET['key'];
if($id){
    $c->toQueryOne($id);
}else if($key){
	$value = $_GET['value'];
    $c->getFieldAll($key,$value);
}else{
    $c->toQueryList();
}