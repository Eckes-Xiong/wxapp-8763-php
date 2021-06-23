<?php
class QueryController {

    public $model = null;
    public $table = null;

    public function __construct($table){
        //实例化model
        $this->table = $table?$table:'top_byword';
        $this->model = new QueryModel($this->table);// 在model中通过 $this->tb 获取
    }
    //查询所有文章列表
    public function toQueryList(){
        $list = $this->model->getAll();
        return $this->model->getJsonData(1,'',$list);
    }
    //查询1条文章
    public function toQueryOne($id,$column){
		$column = $column ? $column : 'id';
        $one = $this->model->getField($column,$id);
        return $this->model->getJsonData(1,'',$one);
    }
    //随机查询1条文章
    public function toQueryOneRandom(){
        $one = $this->model->getFieldRandom();
        return $this->model->getJsonData(1,'',$one);
    }
    //条件查询
    public function getFieldAll($fieldName,$field,$type='='){
        $all = $this->model->getFieldAll($fieldName,$field,$type);
        return $this->model->getJsonData(1,'',$all);
    }
	//按字段查询
	public function getColumnsToFieldAll($columns, $fieldName,$field,$type='=',$limit,$sort){
        $total = array_shift($this->model->getCount('COUNT(*)',' WHERE '.$fieldName.$type.'"'.$field.'"'));
        $all = $this->model->getColumnsToFieldAll($columns,$fieldName,$field,$type,$limit,$sort);
        return $this->model->getJsonData(1,'',$all,$total);
    }
	//删除
	public function delOneInfomation($id){
        $all = $this->model->delOneInfomation($id);
        return $this->model->getJsonData(1,'',$all);
    }
	//查询最近几天的每一天的条数
	public function getCountEveryDay($dayNum=9,$fieldName='list_createDateTime'){
		$sql = "SELECT date_format($fieldName,'%Y/%m/%d') as 'createDate',count($fieldName) as 'total' FROM $this->table where DATE_SUB(CURDATE(), INTERVAL $dayNum DAY) <= date_format($fieldName,'%Y/%m/%d') GROUP BY date_format($fieldName,'%Y/%m/%d')";
		$all=$this->model->sendSql($sql);
		return $this->model->getJsonData(1,'',$all);
	}
}