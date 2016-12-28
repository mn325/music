<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Weixin\Controller;

use Think\Controller;
use Com\Wechat;
use Com\WechatAuth;

class IndexController extends Controller{
    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    
    public function index($id = ''){
        $appid = 'wx954b13d70fa711a6'; //AppID(应用ID)
        $token = 'weixin'; //微信后台填写的TOKEN
        $crypt = '7nRBoXsxMODMV99RlIy5pu5Www4ht0drkvhM2FP9Zkx '; //消息加密KEY（EncodingAESKey）
        
        /* 加载微信SDK */
        $wechat = new Wechat($token, $appid, $crypt);
        
        /* 获取请求信息 */
        $data = $wechat->request();

        if($data && is_array($data)){
            /**
             * 你可以在这里分析数据，决定要返回给用户什么样的信息
             * 接受到的信息类型有10种，分别使用下面10个常量标识
             * Wechat::MSG_TYPE_TEXT       //文本消息
             * Wechat::MSG_TYPE_IMAGE      //图片消息
             * Wechat::MSG_TYPE_VOICE      //音频消息
             * Wechat::MSG_TYPE_VIDEO      //视频消息
             * Wechat::MSG_TYPE_SHORTVIDEO //视频消息
             * Wechat::MSG_TYPE_MUSIC      //音乐消息
             * Wechat::MSG_TYPE_NEWS       //图文消息（推送过来的应该不存在这种类型，但是可以给用户回复该类型消息）
             * Wechat::MSG_TYPE_LOCATION   //位置消息
             * Wechat::MSG_TYPE_LINK       //连接消息
             * Wechat::MSG_TYPE_EVENT      //事件消息
             *
             * 事件消息又分为下面五种
             * Wechat::MSG_EVENT_SUBSCRIBE    //订阅
             * Wechat::MSG_EVENT_UNSUBSCRIBE  //取消订阅
             * Wechat::MSG_EVENT_SCAN         //二维码扫描
             * Wechat::MSG_EVENT_LOCATION     //报告位置
             * Wechat::MSG_EVENT_CLICK        //菜单点击
             */

            //记录微信推送过来的数据
            //file_put_contents('./data.json', json_encode($data));

            /* 响应当前请求(自动回复) */
            //$wechat->response($content, $type);

            /**
             * 响应当前请求还有以下方法可以使用
             * 具体参数格式说明请参考文档
             * 
             * $wechat->replyText($text); //回复文本消息
             * $wechat->replyImage($media_id); //回复图片消息
             * $wechat->replyVoice($media_id); //回复音频消息
             * $wechat->replyVideo($media_id, $title, $discription); //回复视频消息
             * $wechat->replyMusic($title, $discription, $musicurl, $hqmusicurl, $thumb_media_id); //回复音乐消息
             * $wechat->replyNews($news, $news1, $news2, $news3); //回复多条图文消息
             * $wechat->replyNewsOnce($title, $discription, $url, $picurl); //回复单条图文消息
             * 
             */
            
            //执行Demo
            $this->demo($wechat, $data);
        }
       
        
    }

    /**
     * DEMO
     * @param  Object $wechat Wechat对象
     * @param  array  $data   接受到微信推送的消息
     */
    private function demo($wechat, $data){
        switch ($data['MsgType']) {
            // 事件类型
            case Wechat::MSG_TYPE_EVENT:
                switch ($data['Event']) {
                    //订阅
                    case Wechat::MSG_EVENT_SUBSCRIBE:
                        //订阅者写入数据库
                        D('User')->addUser($data['FromUserName']);
                        //订阅记录日志
                        $wechat->replyText("十年前听歌使用磁带；五年前听歌使用MP3；如今听歌使用手机APP...时代在发展科技在进步，我们的生活方式也在逐步发生改变，而你正是这一切改变的见证者，唯一不变的是你对生活的那份热爱！欢迎关注极会玩公众微信号，在这里你不用下载任何APP就可以体验到那些强大的功能，回复关键字即可得到想要的功能，详细的玩法请点击下方菜单。\n^_^ enjoying it...");
                        break;
                    //取消订阅
                    case Wechat::MSG_EVENT_UNSUBSCRIBE:
                        //取消关注，记录日志
                        D('User')->delUser($data['FromUserName']);
                        break;

                    default:
                        $wechat->replyText("极会玩提醒您！您的事件类型：{$data['Event']}，EventKey：{$data['EventKey']}");
                        break;
                }
                break;

            case Wechat::MSG_TYPE_TEXT:
                $res=$this->respond($data['Content'],$data['FromUserName']);

                $wechat->response($res['content'], $res['type']);
                break;
            case Wechat::MSG_TYPE_VOICE:
                $keyword=str_replace('。','',$data['Recognition']);
                $res=$this->respond($keyword,$data['FromUserName']);
                $wechat->response($res['content'], $res['type']);
                break;
        }
    }


    private function respond($keyword,$openid){
        if($keyword!=''){
            if($openid=='oxhZjwQ00xr7kC9uIg8NjLMox7xo'&&preg_match('/^#(.*)/i',$keyword,$arr)){//管理员操作开始
                //查看留言
                if($arr[1]=='查看留言'){
                    return D('Comment')->getComment();
                //删除留言
                }elseif(preg_match('/^删除(\d+)/ui',$arr[1],$num)){
                    return D('Comment')->delComment($num[1]);
                }


                
            
            }//管理员操作结束

            
            if(preg_match('/^听(.*)/ui',$keyword,$arr)){
                return D('Api')->getMusic($arr[1]);//搜索歌曲

            }elseif(preg_match('/^看(.*)/ui',$keyword,$arr)){
                return D('Api')->getMv($arr[1]); //搜索mv 
                
            }elseif(preg_match('/(.*)天气$/ui',$keyword,$arr)){
                return D('Api')->getWeather($arr[1]);//查询天气
              
            }elseif(preg_match('/^@(.*)/i',$keyword,$arr)){//意见反馈
                 return D('Comment')->setComment($arr[1],$openid);

            }elseif(preg_match('/^快递(\d*)/i',$keyword,$arr)){//查快递
                if(!empty($arr[1])){//输入快递单号查询
                    $res=D('Api')->getExpress($arr[1]);
                    if($res){
                        $db=M('Express');
                        $where=array('openid'=>$openid);
                        if($db->where($where)->find()){
                            $db->where($where)->setField('express',$arr[1]);
                        }else{
                            $data=array('openid'=>$openid,'express'=>$arr[1]);
                            $db->add($data);
                        }
                        $str=$res;
                    }else{
                        $str='输入的快递单号不存在或者已经过期！';
                    }
                }else{//未输入快递单号,查询数据库获取上次查询的快递单号
                    $express=M('Express')->where(array('openid'=>$openid))->getField('express');
                    if(!$express){
                        $str='未输入快递单号！';
                    }else{
                        $str=D('Api')->getExpress($express);
                    }
                    
                }
                return array('content'=>$str,'type'=>'text');


            }elseif($keyword=='头条'){//看新闻
                return D('Api')->getNews();

            }elseif($keyword=='穿越'){//历史上的今天
                return D('Api')->getHistory();

            }elseif($keyword=='gq2016'){//图片地址
                return array('content'=>'http://pan.baidu.com/s/1nu9q2Bv','type'=>'text');

            }else{//否者由图灵返回结果
                return D('Api')->robot($keyword);
            }
        }else{
            echo "Input something...";
        }
    }
}
