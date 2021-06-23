<?php
class PageController {

    public $type = 'release';
    public $model = null;
    public $table = null;
    private $html = null;

    public function __construct($table){
        //实例化model
        $this->table = $table;
        $this->model = new PageModel($this->table);// 在model中通过 $this->tb 获取
    }
    /**
     * @params
     *
     * */
    public function getExec($id){
        $this->html = $this->model->getExec($id);
        //通用html head
        require_once VIEW_PATH.'/'.$GLOBALS['config']['app']['index_head'].'.php';
        //通用 head nav
        require_once VIEW_PATH.'/'.$GLOBALS['config']['app']['index_nav'].'.php';
        //导入主体内容view
        require_once VIEW_PATH.'/'.$this->type.'/'.$GLOBALS['config'][$this->type]['view_main'].'.php';
        //通用html foot
        require_once VIEW_PATH.'/'.$GLOBALS['config']['app']['index_foot'].'.php';
    }

} 