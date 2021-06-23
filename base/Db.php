<?php
/**
 * PDO db封装
 * Time: 10:15
 */
header('Content-Type: text/html; charset=utf-8');
class Db {
    /* @dbConfig[]
     * 默认db配置，值会被config.db.php覆盖
     * */
    private $dbConfig = array(
        'dbType' => 'mysql'
        ,'host' => 'localhost'
        ,'user' => 'root'
        ,'pass' => 'root'
        ,'charset' => 'utf8'
        ,'dbname' => 'qdm0440205_db'
        ,'port' => '3306'
    );

    public $conn = '';
    public $num = null;
    public $lastId = null;
    private static $instance = '';

    public static function getInstance($params=array()){
        if(!self::$instance instanceof self){
            self::$instance = new self($params);
        }
        return self::$instance;
    }
    private function __clone(){}

    private function __construct($params=array()){
        $this->dbConfig = array_merge($this->dbConfig,$params);
        $this->connect();
    }

    private function connect(){
        try{
            $dns = "{$this->dbConfig['dbType']}:host={$this->dbConfig['host']};port={$this->dbConfig['port']};
                    charset={$this->dbConfig['charset']};dbname={$this->dbConfig['dbname']}";

            $this->conn = new PDO($dns,$this->dbConfig['user'],$this->dbConfig['pass']);
            $this->conn->query("SET NAMES {$this->dbConfig['charset']}");
            //echo '<script>console.log("连接成功！")</script>';
        }catch (PDOException $e){
            echo '<script>console.log("连接错误！")</script>';
            die($e->getMessage());
        }
    }

    public function exec($sql){
        $num = $this->conn->exec($sql);

        if($num>0){
            if($this->conn->lastInsertId() != null){
                $this->lastId = $this->conn->lastInsertId();
            }
            $this->num = $num;
            //echo '<script>console.log("成功操作了'.$this->num.'条数据！")</script>';
        }else{
            $error = $this->conn->errorInfo();
            //var_dump($error);
            die($error[0].$error[1].$error[2]);
        }
    }

    public function fetch($sql){
        $row = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
    /** 遍历数据方式
     * foreach($rows as $row){ echo $row[ASSOC];}
     * */
    public function fetchAll($sql){
        //echo '<script>console.log("总共查询到'.$this->conn->query($sql)->rowCount().'条数据！")</script>';
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    //返回受 DELETE、INSERT、 或 UPDATE 语句影响的行数。
    public function rowCount($sql){
        return $this->conn->query($sql)->rowCount();
    }
} 