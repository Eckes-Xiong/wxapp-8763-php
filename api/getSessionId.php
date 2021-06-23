<?php
require '../admin_entry.php';
$result = array(
	'code' => 1,
	'message' => 'success',
	'data' => $_SESSION['u_id']
);
echo json_encode($result);