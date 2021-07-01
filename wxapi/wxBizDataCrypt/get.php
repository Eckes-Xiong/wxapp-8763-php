<?php
require '../../admin_entry.php';
include_once "wxBizDataCrypt.php";
$header = getallheaders();
if($header["token"]!=$_SESSION['token']){
    http_response_code(401);
    exit(json_encode(array(
        'code' => 401,
        'message' => '登录验证失败！',
        'data' => array(
            "header"=> $header["token"],
            "session"=> $_SESSION
        )
    ), JSON_UNESCAPED_UNICODE));
    exit;
}
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
