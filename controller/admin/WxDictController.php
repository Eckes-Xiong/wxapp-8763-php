<?php
class WxDictController {

    public function __construct($table,$view){
        //实例化model
        $this->table = $table?$table:'wxapp_dict';
        $this->view = $view;
        $this->model = new WxDictModel($this->table);
    }
    //search home
    public function getHomeData(){
        //首页广告
        $sql1 = 'SELECT cover,name,url FROM wxapp_adv WHERE status=1 ORDER BY sort DESC';
        $data1 = $this->model->getSqlAll($sql1);
        //首页店铺
        //$sql2 = 'SELECT cover,bg,name,level,subtitle,type FROM wxapp_store WHERE status=1 ORDER BY sort DESC LIMIT 0,5';
        //$data2 = $this->model->getSqlAll($sql2);
        //首页商品列表
        //
        $sql4 = 'SELECT id,cover,name,subtitle,price,discount,typedesc FROM wxapp_product_list WHERE vipSort<100 and typecode like "%201003" ORDER BY vipSort DESC LIMIT 0,8';
        $data4 = $this->model->getSqlAll($sql4);
        //
        $sql3 = 'SELECT id,cover,name,subtitle,price,discount,typedesc FROM wxapp_product_list WHERE vipSort<100 and typecode like "%201026" ORDER BY vipSort DESC LIMIT 0,8';
        $data3 = $this->model->getSqlAll($sql3);
        $result = array(
            'code' => 1,
            'message' => "success",
            'data' => array(
                'adv' => $data1,
                //'store' => $data2,
                'goods' => array(
                    array(
                        'name' => "坚果蜜饯",
                        'list' => $data4,
                    ),
                    array(
                        'name' => "苏打水",
                        'list' => $data3,
                    )
                )
            )
        );
        echo json_encode($result);
        exit;
    }
    //search one
    public function handleSearchOne($sql){
        $data = $this->model->getsqlOne($sql);
        $this->model->getJsonData(1,'success',$data);
    }
    //search one
    public function handleSearchAll($sql){
        $data = $this->model->getSqlAll($sql);
        $this->model->getJsonData(1,'success',$data);
    }
    //insert
    public function handleInsert($sql){
        $this->model->exec($sql);
        //insert
        if($this->model->insertId != null && $this->model->insertId != 0){
            //$this->model->creatPage($fields['operate']);
            $this->model->getJsonData(1,'添加成功！');
        }
    }
    //update
    public function handleUpdate($sql){
        $this->model->exec($sql);
        $this->model->getJsonData(1,'修改成功！');
    }

    //查询dict
    public function toGetDict($id){
        $arr = $this->model->getFieldAll("parentcode",$id);
        return $this->model->getJsonData(1,'success',$arr);
    }
    //admin查询首页大广告
    public function toGetAdv(){
        $sql = 'SELECT * FROM '.$this->table.' ORDER BY sort DESC';
        $data = $this->model->getSqlAll($sql);
        return $this->model->getJsonData(1,'success',$data);
    }

    //申请开店
    public function insertNewStore($fields){
        $sql = "SELECT COUNT(1) FROM ".$this->table." WHERE name='".$fields["name"]."'";
        $data = $this->model->getsqlOne($sql);
        if($data["COUNT(1)"]!=0){
            $this->model->getJsonData(0,'店铺名称已存在！');
            exit;
        }
        $this->model->storeExec($fields,'insert');
        //insert
        if($this->model->insertId != null && $this->model->insertId != 0){
            //$this->model->creatPage($fields['operate']);
            $this->model->getJsonData(1,'提交成功！');
        }
    }
    //店铺审核
    public function setStore($status,$o){
        $sql = "UPDATE ".$this->table." SET status='".$status."' WHERE openid='".$o."'";
        $this->model->exec($sql);
        $sql2 = "UPDATE wxapp_user SET isStore='".$status."' WHERE openid='".$o."'";
        $this->model->exec($sql2);
        $this->model->getJsonData(1,'修改成功！');
    }
    //修改店铺信息
    public function updateStore($fields){
        $this->model->storeExec($fields,'update');
        $this->model->getJsonData(1,'修改成功！');
    }

    //查询user自己的产品
    public function postUserProducts($fields){
        $sql = 'SELECT id,name,typecode,typedesc,sort,cover,size,subtitle,content,tags,active,price,discount,status,sales,code,isEvent,color FROM '.$this->table." WHERE typecode like '".$fields["type"]."%' and openid='".$_SESSION['openid']."' limit ".(($fields["pageNum"]-1)*$fields["pageSize"]).",".$fields["pageSize"];
        $data = $this->model->getSqlAll($sql);
        $this->model->getJsonData(1,'',$data);
    }
    //user 新增产品
    public function insertUserProduct($fields){
        $this->model->productExec($fields,'insert');
        //insert
        if($this->model->insertId != null && $this->model->insertId != 0){
            //$this->model->creatPage($fields['operate']);
            $this->model->getJsonData(1,'发布成功！');
        }
    }
    //user 修改产品
    public function updateUserProduct($fields){
        $this->model->productExec($fields,'update');
        $this->model->getJsonData(1,'更新成功！');
    }
} 