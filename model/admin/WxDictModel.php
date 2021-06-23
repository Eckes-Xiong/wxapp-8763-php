<?php
class WxDictModel extends Model {


  //insert store
  public function storeExec($fields,$type){
    if($type=='insert'){

        $name = $this->getSqlAll("SELECT dictname FROM wxapp_dict WHERE dictcode in(".$fields['type'].")");
        $len = count($name);
        $str = "";
        for ($i=0; $i < $len; $i++) { 
          $str .= $name[$i]["dictname"];
          if($i !== $len-1){
            $str .=",";
          }
        }

        $sql = "INSERT INTO ".$this->tb." (
        address,name, cover, openid,
        subtitle, bg, type, status, typeName
        ) VALUES (
        '".$fields['address']."','".$fields['name']."', '".$fields['cover']."',
        '".$_SESSION['openid']."', '".$fields['subtitle']."',
        '".$fields['bg']."', '".$fields['type']."',
        '0','".$str."'
        )";

    }else if($type=='update'){
        $sql = "UPDATE ".$this->tb." SET 
        cover='".$fields['cover']."', 
        address='".$fields['address']."', 
        subtitle='".$fields['subtitle']."', 
        bg='".$fields['bg']."' WHERE id='".$fields['id']."'";
    }
    $this->exec($sql);
  }
//insert product
  public function productExec($fields,$type){
    if($type=='insert'){

        $sql = "INSERT INTO ".$this->tb." (
        name, cover, openid,
        subtitle, price, discount,
        content, size,
        sort, typedesc,
        typecode, color,
        status, active, isEvent
        ) VALUES (
        '".$fields['name']."', '".$fields['cover']."',
        '".$_SESSION['openid']."', '".$fields['subtitle']."',
        '".$fields['price']."', ".$fields['discount'].",
        '".$fields['content']."', '".$fields['size']."',
        '".$fields['sort']."', '".$fields['typedesc']."',
        '".$fields['typecode']."','".$fields['color']."',
        '0','1','".$fields['isEvent']."'
        )";

    }else if($type=='update'){
        $sql = "UPDATE ".$this->tb." SET name = '".$fields['name']."', 
            cover = '".$fields['cover']."',
            subtitle = '".$fields['subtitle']."', 
            updateTime = '".date("Y-m-d H:i:s")."',
            price = '".$fields['price']."', 
            discount = ".$fields['discount'].",
            content = '".$fields['content']."', 
            size = '".$fields['size']."',
            isEvent = '".$fields['isEvent']."',
            sort = '".$fields['sort']."', 
            color = '".$fields['color']."', 
            typedesc = '".$fields['typedesc']."',
            typecode = '".$fields['typecode']."' WHERE id = '".$fields["id"]."'";
    }
    $this->exec($sql);
  }
}