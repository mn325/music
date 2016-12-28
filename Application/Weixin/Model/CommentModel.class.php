<?php
namespace Weixin\Model; 

use Think\Model;

class CommentModel extends Model {
    //查看评论
    public function getComment() {
        $where=array('isread'=>0);
        $field=array('id','time','content');
        $res=$this->field($field)
        ->where($where)
        ->order('time desc')
        ->select();
        $this->where($where)->setField('isread',1);
        if($res){
            $str='';
            foreach($res as $k=>$v){
                $str.=$v['id']."\n".date('Y/m/d H:i:s',$v['time'])."\n".$v['content']."\n";
            }
        }else{
            $str='暂无未读留言！';
        }
        return array('content'=>$str,'type'=>'text');
    }
    //删除留言
    public function delComment($id) {
        $res=$this->delete($id);
        if($res){
            $str='删除'.$num[1].'成功！';
        }else{
            $str='删除'.$num[1].'失败！';
        }
        return array('content'=>$str,'type'=>'text');
    }
    //留言
    public function setComment($key,$openid){
        if($key!=''){
            $data=array('openid'=>$openid,'content'=>$key,'time'=>time(),'isread'=>0);
            $res=$this->add($data);
            if($res){
                $str="感谢您的留言！^_^\n点击下方留言板菜单进入阅读原文即可查看。";
            }else{
                $str='好像失败了~~';
            }
        }else{
           $str='请输入留言内容！'; 
        }
        return array('content'=>$str,'type'=>'text');
    }
    
}