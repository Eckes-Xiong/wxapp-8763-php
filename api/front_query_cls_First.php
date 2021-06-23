<?php
require '../admin_entry.php';
$c = new QueryController('top_infomation_classify_1');
//获取所有1级分类
$columns ='
cls_id,
cls_name,
cls_name_en,
cls_sid';
$c->getColumnsToFieldAll($columns,'cls_id','0','>')