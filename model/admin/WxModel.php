<?php
class WxModel extends Model {
    //getSlideItems
    public function getSlideItems(){
        $sql = "SELECT * FROM ".$this->tb." WHERE isShow=1 limit 0,5";
        return $this->getSqlAll($sql);
    }
}