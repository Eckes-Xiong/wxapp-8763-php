<?php
class IndexController {

    public $model = null;
    public $table = null;
    public $view = null;

    public function __construct($table,$view){
        //实例化model
        $this->table = $table;
        $this->view = $view;
        $this->model = new IndexModel($this->table);
    }

    //初始化后台视图view
    public function showPageAction(){
        $sign = $this->table;
        $this->model->getView($sign,$this->view);
    }

    /**
     * nav_a_list 视图
     * */
    public function listPage(){
        require VIEW_PATH."/admin/index_space_".$this->view.".php";
    }
} 