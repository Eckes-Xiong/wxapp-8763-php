<?php
class ReleaseController {

    public $model = null;
    public $table = null;

    public function __construct($table){
        //实例化model
        $this->table = $table?$table:'top_infomation_list';
        $this->model = new ReleaseModel($this->table);// 在model中通过 $this->tb 获取
    }
    /** 新增 marry
     * @params
     *   $fields => $_POST
     * */
    public function setMarry_yk_message($data){

        $_data = json_decode($data,true);
        if(empty($_data['name'])){
            $this->getJsonData(10301,'名字不可以为空');
        }else if(empty($_data['message'])){
            $this->getJsonData(10302,'内容不可以为空');
        }else{
            $sql = "INSERT INTO ".$this->model->tb."(
                name, tel, message, exp
                ) VALUES (
                '".$_data['name']."', '".$_data['tel']."', '".$_data['message']."',
                '".$_data['exp']."'
            )";
            $this->model->exec($sql);
        }

        //insert
        if($this->model->insertId != null && $this->model->insertId != 0){
            //$this->model->creatPage($fields['operate']);
            $this->model->getJsonData(1,'提交成功，已收到您的祝福！');
        }
    }
    public function marry_yk_people($data){
        $data = json_decode($data,true);
        if(empty($data["name"])){
            $this->getJsonData(10301,'名字不可以为空');
        }else{
            $sql = "INSERT INTO ".$this->model->tb." (name) VALUES ('".$data["name"]."')";
            $this->model->exec($sql);
        }

        //insert
        if($this->model->insertId != null && $this->model->insertId != 0){
            //$this->model->creatPage($fields['operate']);
            $this->model->getJsonData(1,'提交成功，期待您的到来！');
        }
    }
    /** 新增
     * @params
     *   $fields => $_POST
     * */
    public function getNewExec($fields){
        $this->model->isEmpty($fields,'insert');

        //insert
        if($this->model->insertId != null && $this->model->insertId != 0){
            //$this->model->creatPage($fields['operate']);
            $this->model->getJsonData(1,'提交成功！');
        }
    }
    /** 更新
     * @params
     *   $fields => $_POST
     * */
    public function getUpdateExec($fields){
        $this->model->isEmpty($fields,'update');
        $this->model->getJsonData(1,'修改成功！');
    }
} 