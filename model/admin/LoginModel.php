<?php
class LoginModel extends Model {
    //添加注册用户信息
    public function getExec($fields){
        $pass = md5($fields['password']."eckes.cn.1989");

        $sql = "INSERT INTO ".$this->tb." (l_user, l_pass, l_phone, l_email) VALUES
                (
                '".$fields['user']."',
                '".$pass."',
                '".$fields['phone']."',
                '".$fields['email']."'
                )";
        $this->exec($sql);
    }

    //登录验证
    public function getSign($fields){
        //$pass = md5($fields->password."eckes.cn.1989");
		$pass = $fields->password;
        $sql = "SELECT * FROM ".$this->tb." WHERE username='{$fields->username}' and password='{$pass}'";
        //print_r($fields);
        //echo $sql;
        return $this->getsqlOne($sql);
    }

    //wxapp 刷新每日任务
    public function refreshDailyTask($flag){
        $data = $this->getsqlOne("SELECT dailyStartDate FROM wxapp_task_user where openid='{$_SESSION['openid']}'");
        $now = strtotime(date("Y-m-d"));//当前请求任务中心的 时间
        $nowWeek = date("w"); //当前星期几 1234560
        // date("Y-m-d",strtotime(date("Y-m-d"))+(8-date("w"))*60*60*24)
        //上次请求任务中心的 时间
        $oldDaily = strtotime($data["dailyStartDate"]);
        if($now>$oldDaily){
          //表示过了一天，该刷新每日任务了
          $daily = 'daily="0,0,0,0,0"';
          $weekly = "";
          $dsd = ',dailyStartDate="'.date("Y-m-d").'"';
          //过了几天
          $pass = ($now-$oldDaily)/(60*60*24);
          if($nowWeek==0){
            $nowWeek=7;
          }
          if(7-$nowWeek < $pass){
            //表示今天星期一，到了新的一周，该刷新周任务了
            $weekly = ',weekly="0,0,0,0,0"';
          }
          $vip=',vip="100,0,0,0,0,0"';
          //更新每日任务
          $this->exec('UPDATE wxapp_task_user SET '.$daily.$weekly.$dsd.$vip.' WHERE openid="'.$_SESSION['openid'].'"');
        }
        if($flag){
            $res = $this->getsqlOne("SELECT id,daily,weekly,time,vip,resident,dailyStartDate FROM wxapp_task_user where openid='{$_SESSION['openid']}'");
            return $res;
        }
    }
    public function updateUserTask($set){
        $this->exec("UPDATE wxapp_task_user SET {$set} WHERE openid='{$_SESSION['openid']}'");
    }
}