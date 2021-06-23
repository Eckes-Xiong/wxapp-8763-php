<?php
require '../../admin_entry.php';
include_once "wxBizDataCrypt.php";
$app->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);

$appid = 'wx45c8d1b497f3b936';
$encryptedData=$json["encryptedData"];
$iv = $json["iv"];

$pc = new WXBizDataCrypt($appid, $_SESSION['session_key']);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    $data=json_decode($data);
    $result = array(
        'code' => 1,
        'message' => "success",
        'data' => $data->purePhoneNumber
    );
    echo json_encode($result);
    exit;
} else {
    $result = array(
        'code' =>"0",
        'message' => $errCode,
        'data' => $errCode
    );
    echo json_encode($result);
    exit;
}
