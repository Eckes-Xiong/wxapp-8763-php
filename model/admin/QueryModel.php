<?php
class QueryModel extends Model {
    //
	//list
	public function getColumnsToFieldAll($columns, $fieldName,$field,$type='=',$limit,$sort){
		$sql = 'SELECT '.$columns.' FROM '.$this->tb.' WHERE '.$fieldName.$type.'"'.$field.'"'.$sort." ".$limit;
		return $this->db->fetchAll($sql);
	}
	public function delOneInfomation($id){
		$sql = 'DELETE FROM '.$this->tb.' WHERE list_id="'.$id.'"';
        return $this->db->exec($sql);
	}
	public function getOne($fieldName,$field,$type='='){
		$sql = 'SELECT * FROM '.$this->tb.' WHERE '.$fieldName.$type.'"'.$field.'"';
        return $this->db->fetch($sql);
	}
	public function sendSql($sql){
		return $this->db->fetchAll($sql);
	}
}