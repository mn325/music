<?php

namespace Weixin\Controller;

use Think\Controller;

class CommentController extends Controller{
    //加载留言
    public function messages(){
        $res=M('Comment')->order('time desc')->select();
        $this->assign('res',$res);
        $this->display('suggest');
    }
    //处理留言
    public function setMessage(){
        $content=I('post.content');
        $content=trim($content);
        if($content){
            //获取ip及地址
            $ip=get_client_ip();
            $res=file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip);
            $res=json_decode($res,true);
            $address=isset($res['province'])||isset($res['city'])?$res['province'].$res['city']:'火星';
            if($res['province']==$res['city']){
                $address=$res['city'];
            }
            $data=array('content'=>$content,'time'=>time(),'ip'=>$ip,'address'=>$address);
            $res=M('Comment')->add($data);
            if($res!==fale){
                $this->ajaxReturn(array('content'=>$content,'address'=>$address));
            }
            
        }
        $this->ajaxReturn(false);

    }
}