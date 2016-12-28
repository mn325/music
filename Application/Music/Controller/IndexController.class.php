<?php
namespace Music\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){   
        $agent=is_mobile();
        if($agent==false){
            $this->display('music');
        }else{
            $this->show('<div style="font-size:48px">您使用的是'.$agent.'设备，暂未开发移动端页面，为了更好的体验效果请使用电脑访问。<br/>还可以微信查找“极会玩”或扫描下方二维码来关注极会玩微信公众号体验不一样的玩法。</div><div style="margin:0 auto;width:600px"><img src="__PUBLIC__/images/weixin.jpg" style="width:600px"/></div>', 'utf-8');
        }
    }
}