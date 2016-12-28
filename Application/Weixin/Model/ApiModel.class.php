<?php
namespace Weixin\Model;

use Common\Common\CloudMusic;

class ApiModel {
    //获取头条信息
    public function getNews() {
        //新浪API
        /*$url='http://api.sina.cn/sinago/list.json?channel=news_toutiao';
        $news=file_get_contents($url);
        $res=json_decode($news,true);
        $news=$res['data']['list'];
        $new=array();
        foreach($news as $k=> $v){
            if(isset($v['pics'])){
                $new[]=$news[$k];
            }
        }
        $res=array();
        $count=count($new)>5?5:count($new);
        for($i=0;$i<$count;$i++){
            $res[$i][]=$new[$i]['title'];
            $res[$i][]=$new[$i]['intro'];
            $res[$i][]=$new[$i]['kpic'];
            $res[$i][]=$new[$i]['link'];
        }
        return array('content'=>$res,'type'=>'news');*/
        //百度API
        $ch = curl_init();
        $url = 'http://apis.baidu.com/showapi_open_bus/channel_news/search_news';
        $header = array(
            'apikey: 623a04e2eac33377fc379e454f9b7d98',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        
        $res=json_decode($res,true);
        $res=$res['showapi_res_body']['pagebean']['contentlist'];
        $news=array();
        foreach($res as $k => $v){
            if(isset($v['desc'])&&!empty($v['imageurls'])){
                $news[]=$res[$k];
            }
        }
        $count=count($news)>7?7:count($news);
        for($i=0;$i<$count;$i++){
            $new[$i][]=$news[$i]['title'];
            $new[$i][]=$news[$i]['desc'];
            $new[$i][]=$news[$i]['imageurls'][0]['url'];
            $new[$i][]=$news[$i]['link'];
        
        }
        return array('content'=>$new,'type'=>'news');
    }
    //搜索歌曲
    public function getMusic($key){
        $obj=new CloudMusic();
        $arr=json_decode($obj->music_search($key, "1"),true);
        if($musicUrl=$arr['result']['songs'][0]['mp3Url']){
            $title=$arr['result']['songs'][0]['name'];
            $desc='歌手：'.$arr['result']['songs'][0]['artists'][0]['name'];
            $musicUrl=$arr['result']['songs'][0]['mp3Url'];
            $content=array($title,$desc,$musicUrl,$musicUrl);
            return array('content'=>$content,'type'=>'music');
        }else{
            $content='暂未收录此歌曲，试试其它的吧！-_-!';
            return array('content'=>$content,'type'=>'text');
        } 
    }
    //搜索mv
    public function getMv($key) {
        $obj=new CloudMusic();
        $arr=json_decode($obj->music_search($key, "1"),true);
        if($mvid=$arr['result']['songs'][0]['mvid']){
            $title=$arr['result']['songs'][0]['name'];
            $desc='演唱者：'.$arr['result']['songs'][0]['artists'][0]['name'];
            $picUrl=$arr['result']['songs'][0]['album']['picUrl'];
            $mp4=json_decode($obj->get_mv_info($mvid),true);
            $url=isset($mp4['data']['brs']['720'])?$mp4['data']['brs']['720']:(isset($mp4['data']['brs']['480'])?$mp4['data']['brs']['480']:$mp4['data']['brs']['270']);
            $content=array(array($title,$desc,$picUrl,$url));
            return array('content'=>$content,'type'=>'news');
        }else{
            $content='暂未收录此MV，试试其它的吧！-_-!';
            return array('content'=>$content,'type'=>'text');
        }
    }
    //搜索天气
    public function getWeather($key){
        $weather=file_get_contents('http://api.map.baidu.com/telematics/v3/weather?location='.$key.'&output=json&ak=IitK9LlBV3lVnY2IoyYGhzqoTASPsKlO');
        $arr=json_decode($weather,true);
        if($arr['error']==0){
            $str='';
            $str.=$arr['results'][0]['currentCity']."天气：\n";
            $str.=$arr['results'][0]['weather_data'][0]['date']."\n";
            $str.=$arr['results'][0]['weather_data'][0]['weather'].','.$arr['results'][0]['weather_data'][0]['wind'].','.$arr['results'][0]['weather_data'][0]['temperature']."\n";
            foreach($arr['results'][0]['index'] as $k=>$v){
                $str.=$arr['results'][0]['index'][$k]['tipt'].':'.$arr['results'][0]['index'][$k]['zs']."\n";
                $str.=$arr['results'][0]['index'][$k]['des']."\n";
            }
            $str.=$arr['results'][0]['weather_data'][1]['date'].':'.$arr['results'][0]['weather_data'][1]['weather'].','.$arr['results'][0]['weather_data'][1]['wind'].','.$arr['results'][0]['weather_data'][1]['temperature']."\n";
            $str.=$arr['results'][0]['weather_data'][2]['date'].':'.$arr['results'][0]['weather_data'][2]['weather'].','.$arr['results'][0]['weather_data'][2]['wind'].','.$arr['results'][0]['weather_data'][2]['temperature']."\n";
            $str.=$arr['results'][0]['weather_data'][3]['date'].':'.$arr['results'][0]['weather_data'][3]['weather'].','.$arr['results'][0]['weather_data'][3]['wind'].','.$arr['results'][0]['weather_data'][3]['temperature']."\n";
    
        }else{
            $str='输入的城市有误哦！ -_-!';
        }
        return array('content'=>$str,'type'=>'text');
    }
    //查询快递
    function getExpress($num){
        //$num='1056714759320';
        $res=file_get_contents('https://www.kuaidi100.com/autonumber/autoComNum?text='.$num);
        $res=json_decode($res,true);
        //print_r($res);die;
        $flag=false;
        if(isset($res['auto'][0]['comCode'])){
            $code=$res['auto'][0]['comCode'];
            $msg=file_get_contents('http://www.kuaidi100.com/query?type='.$code.'&postid='.$num);
            $msg=json_decode($msg,true);
            if($msg['status']=='200'){
                $flag=true;
                $str='快递单号：'.$num."\r\n";
                foreach(array_reverse($msg['data']) as $v){
                    $str.='['.$v['time']."]\n".$v['context']."\r\n";
                
                }
            }
        }
        if($flag){
            return $str;
        }else{
            return false;
        }

        
    }
    //获取历史上的今天
    public function getHistory(){
        $news=file_get_contents('http://www.ipip5.com/today/api.php?type=json');
        $res=json_decode($news,true);
        $today=$res['today'];
        $history='';
        foreach($res['result'] as $k=>$v){
            $history.=$v['year'].'年'.$today.'，'.$v['title']."。\n";
        }
        return array('content'=>$history,'type'=>'text');
    }
    //图灵API
    public function robot($keyword){
        $news=file_get_contents('http://www.tuling123.com/openapi/api?key=2c9e8d720fc646638e14f25cdaa61bc0&info='.$keyword.'&userid='.$fromUsername);
        $res=json_decode($news,true);
        return array('content'=>$res['text'],'type'=>'text');
    }

}