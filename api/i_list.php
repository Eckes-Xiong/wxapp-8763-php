<?php
require '../admin_entry.php';

if(!$_GET['type']){
	$app->isLogin();
}

$post = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
$c = new QueryController('top_infomation_list');

if($post){
	if($post->actionType==='DELETE'){
		$c->delOneInfomation($post->list_id);
	}else if($post->actionType==='SEARCH'){
		$c->toQueryOne($post->list_id,'list_id');
	}
}else{
	 $fieldName='list_user';
	 $field=$_SESSION['username'];
	 $type='=';
	 $page=0;
	 $pagesize=10;
	if($_GET['type']=="index"){
		$fieldName='list_id';
		if($_GET['id']){
			//查询单条文章详情
			$columns ='
			list_id, list_subtitle,
			list_yunFile,
			list_content, list_createDateTime';
			$field=$_GET['id'];
		}else{
			//首页查询文章列表
			if($_GET['page']){
				$page = $_GET['page']-1;
			}
			//首页查询文章列表
			if($_GET['pageSize']){
				$pagesize = $_GET['pageSize'];
			}
			$limit = 'limit '.($page*$pagesize).','.$pagesize;
			$sort="ORDER BY list_updateDateTime DESC";
			$where="";
			//cls：分类的名称
			if($_GET['cls']){
				$fieldName = 'list_cls_'.$_GET['level'];
				$field=$_GET['cls'];
			}else{
				$field='0';
				$type='>';
			}
			//排序
			if($_GET['sort']){
				$sort=" ORDER BY list_updateDateTime ".$_GET['sort'];
			}

			$columns ='
			list_id, list_title,
			list_updateDateTime,
			list_cls_First, list_cls_Second, list_cls_Third,
			list_cover, list_description';
		}
	}else{
		$columns ='
		list_id,list_title,
		list_subtitle,list_updateDateTime,
		list_cls_First, list_cls_Second, list_user, 
		list_hotChecked';
	}

	$c->getColumnsToFieldAll($columns,$fieldName,$field,$type,$limit,$sort);
}
