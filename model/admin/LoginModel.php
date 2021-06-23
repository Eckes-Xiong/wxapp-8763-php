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

    //wxPay查询 购物车支付成功之后
    public function AfterWxPay($wxapp){
        //购物车订单
        $status__ = '1'.($wxapp["addrType"]=="ddzt"?"02":"01");
        $this->exec("UPDATE wxapp_order_cart_cache SET status={$status__} WHERE id='{$wxapp['id']}'");
        $this->exec("UPDATE wxapp_order SET status={$status__} WHERE id in({$wxapp["oids"]})");

        //支付成功，更新每日任务，每周任务，常驻任务---------------------------------
        $task = $this->refreshDailyTask(true);
        $t_daily = explode(",", $task["daily"]);
        $t_weekly = explode(",", $task["weekly"]);
        if($t_daily[1]<100){
            $t_daily[1]=100;
        }
        if($wxapp["_amount"]>=4800 && $t_weekly[4]<100){
            $t_weekly[4]=100;
        }
        if($t_weekly[1]<=100){
            $t_weekly[1]=$t_weekly[1]+25;
        }
        $t_resident_txt = $task["resident"];
        if($wxapp["_amount"]>=8800){
            $t_resident = explode(",", $task["resident"]);
            if($t_resident[2]<100){
                $t_resident[2]=100;
                $t_resident_txt = implode(",",$t_resident);
            }
        }
        $t_daily_txt = implode(",",$t_daily);
        $t_weekly_txt = implode(",",$t_weekly);
        $this->updateUserTask("daily='{$t_daily_txt}', weekly='{$t_weekly_txt}', resident='{$t_resident_txt}'");
    
        //添加log，更新积分，更新经验值
        $getIntegral = intval($wxapp["_amount"]/100)*2;
        $_integral=0;
        $this->writeIntegralLog("+{$getIntegral}","购物返积分");
        if($wxapp['integral']){
            $_integral=$wxapp['integral'];
            $this->writeIntegralLog("-{$_integral}","购物使用");
        }
        $this->writePayLog("-{$wxapp['_amount']}", "购物支付");
        $this->exec("UPDATE wxapp_user SET expNum=expNum+{$getIntegral},orderNum=orderNum+1,integralNum=integralNum-{$_integral}+{$getIntegral} WHERE openid='{$_SESSION['openid']}'");
    
        //更新优惠券
        if($wxapp["couponId"]){
            $this->model->exec("UPDATE wxapp_user_coupon SET status=0 WHERE id='{$wxapp['couponId']}'");
        
            $user = $this->getsqlOne("SELECT resident FROM wxapp_task_user where openid='{$_SESSION["openid"]}'");
            $od = explode(",",$user["resident"]);
            if($od[2]<100){
                $od[2] = $od[2]+20;
                $odt = implode(",",$od);
                $this->exec("UPDATE wxapp_task_user SET resident='{$odt}' WHERE openid='{$_SESSION['openid']}'");
            }
        }
        //更新商户余额_amount
        $this->exec("UPDATE wxapp_store SET scope=scope+{$wxapp['_amount']} WHERE id='{$wxapp['sid']}'");
    }
    //wxPay查询 月卡支付成功之后
    public function AfterWxPay2($wxapp){
        
        $me = $this->getsqlOne("SELECT isVip, levelTime FROM wxapp_user where openid='{$_SESSION['openid']}'");
        if($me["isVip"]==1){
            $_time = date("Y-m-d H:i:s", strtotime($me['levelTime'])+24*60*60*30);
        }else{
          $_time = date("Y-m-d H:i:s", time()+24*60*60*30);
        }

        $this->exec("DELETE FROM wxapp_order_cart_cache WHERE id='{$wxapp['id']}'");
        $this->exec("INSERT INTO wxapp_order_vip ( status, openid, out_trade_no) VALUES (776, '".$_SESSION['openid']."', '".$wxapp['out_trade_no']."')");
        $this->exec('UPDATE wxapp_user SET isVip=1, levelTime="'.$_time.'" WHERE openid="'.$_SESSION['openid'].'"');
        $this->exec("INSERT INTO wxapp_user_coupon (cid, openid, endTime) VALUES ('2', '".$_SESSION['openid']."', '".$_time."')");
        $this->writePayLog("-{$wxapp['amount']}","购买满月祝福");

    }
    //wxPay查询 积分储值支付成功之后
    public function AfterWxPay3($json){

        $it = $this->getsqlOne("SELECT * FROM wxapp_integral WHERE id='{$json['id']}'");
        $integral = $it["point"]+$it["p_add"];
        
        $this->exec("INSERT INTO wxapp_order_integral ( openid, out_trade_no ,iid ) VALUES ('{$_SESSION['openid']}', '{$json['out_trade_no']}', '{$json['id']}')");
        $this->exec("UPDATE wxapp_user SET integralNum=integralNum+{$integral} WHERE openid='{$_SESSION['openid']}'");
        
        $this->exec("DELETE FROM wxapp_order_cart_cache WHERE status=3 AND openid='{$_SESSION['openid']}'");
        $this->writeIntegralLog("+{$it['point']}+{$it['p_add']}","积分储值");
    }
}