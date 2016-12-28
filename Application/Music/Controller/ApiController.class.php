<?php
namespace Music\Controller;
use Think\Controller;
use Music\Model\CloudMusic;
class ApiController extends Controller {
    //通过关键字查询歌曲
    public function getMusic(){ 
        $music=new  CloudMusic(); 
        if(isset($_POST['key'])){
            $key=I('post.key');
            $arr=json_decode($music->music_search($key, "1"),true);
        //专辑
            $res=array();
            foreach($arr['result']['songs'] as $k=>$v){
                $res[$k]['id']=$arr['result']['songs'][$k]['id'];
                $res[$k]['mvid']=$arr['result']['songs'][$k]['mvid'];
                $res[$k]['name']=$arr['result']['songs'][$k]['name'];
                $res[$k]['artists']=$arr['result']['songs'][$k]['artists']['0']['name'];
                $res[$k]['mp3Url']=$arr['result']['songs'][$k]['mp3Url'];
                $res[$k]['picUrl']=$arr['result']['songs'][$k]['album']['picUrl'];
                $res[$k]['album']=$arr['result']['songs'][$k]['album']['name'];
                $res[$k]['alid']=$arr['result']['songs'][$k]['album']['id'];
            }
            $this->ajaxReturn($res);
     
        }else{
            $id=$_POST['id'];
            $lrc= (json_decode($music->get_music_lyric($id),true));
            $this->ajaxReturn($lrc['lrc']['lyric']);
        }
    
    }
    //通过id查询mv
    public function getMv() {
        $music=new  CloudMusic();
        $mvid=$_POST['id'];
        $mp4=json_decode($music->get_mv_info($mvid),true);
        $this->ajaxReturn($mp4['data']['brs']);

    }
    //通过专辑id查询专辑
    public function getAlbum() {
        $music=new  CloudMusic();
        $alid=$_POST['alid'];
        $arr=json_decode($music->get_album_info($alid),true);
        $res=array();
        foreach($arr['album']['songs'] as $k=>$v){
            $res[$k]['id']=$arr['album']['songs'][$k]['id'];
            $res[$k]['mvid']=$arr['album']['songs'][$k]['mvid'];
            $res[$k]['name']=$arr['album']['songs'][$k]['name'];
            $res[$k]['artists']=$arr['album']['songs'][$k]['artists']['0']['name'];
            $res[$k]['mp3Url']=$arr['album']['songs'][$k]['mp3Url'];
            $res[$k]['picUrl']=$arr['album']['songs'][$k]['album']['picUrl'];
            $res[$k]['album']=$arr['album']['songs'][$k]['album']['name'];
            $res[$k]['alid']=$arr['album']['songs'][$k]['album']['id'];
        }
        $this->ajaxReturn($res);
    }
    //通过id查询歌曲
    public function cookieMusic() {
        $music=new  CloudMusic();
        $id=$_POST['mid'];
        $arr=json_decode($music->get_music_info($id),true);
        $res=array();
        $res['id']=$arr['songs']['0']['id'];
        $res['mvid']=$arr['songs']['0']['mvid'];
        $res['name']=$arr['songs']['0']['name'];
        $res['artists']=$arr['songs']['0']['artists']['0']['name'];
        $res['mp3Url']=$arr['songs']['0']['mp3Url'];
        $res['picUrl']=$arr['songs']['0']['album']['picUrl'];
        $res['album']=$arr['songs']['0']['album']['name'];
        $res['alid']=$arr['songs']['0']['album']['id'];
        $this->ajaxReturn($res);
    }
}