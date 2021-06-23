<?php
class PageModel extends Model {

    public function getExec($id,$fieldName='i_id'){
        return $this->getField($fieldName,$id);
    }

}