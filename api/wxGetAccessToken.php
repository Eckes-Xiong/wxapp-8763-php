<?php
require '../admin_entry.php';
$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wxee7c129515996768&secret=8a1b472c7fbf698f886e3753c1a03678&js_code='.$_GET['code'].'&grant_type=authorization_code';
echo file_get_contents($url);