<?php
namespace Weixin\Model; 

use Think\Model;

class UserModel extends Model {
    //新增订阅用户
    public function addUser($openid) {
        $where=array('openid'=>$openid);
        $id=$this->where($where)->getField('id');
        if($id){
            $res=$this->where($where)->setField('issubscribe',1);
        }else{
            $data=array('openid'=>$openid,'issubscribe'=>1);
            $res=$this->add($data);
        }
        return $res;
    }

    //用户取消订阅
    public function delUser($openid) {
        $where=array('openid'=>$openid);
        $id=$this->where($where)->getField('id');
        if($id){
            $res=$this->where($where)->setField('issubscribe',0);
        }else{
            $data=array('openid'=>$openid,'issubscribe'=>0);
            $res=$this->add($data);
        }
        return $res;
    }
}