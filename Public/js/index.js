$(function() {
    var currentIndex=1;
    var imgIndex=0;
    $('#mp3')[0].volume=$('#fmVol .fm-vol-slider .ui-slider-range').width()/50;
    var flag=true;
    //播放暂停控制
    $('.fm-play').click(function(){
        if(flag){
          $('#mp3')[0].play();
          $('.fm-play').css({
            'backgroundPosition': '-444px 0'
          });
           // $('.fm-play').hover({
           //  'backgroundPosition': '-490px 0'
           // });
          flag=false; 
        }else{
           $('#mp3')[0].pause();
           $('.fm-play').css({
            'backgroundPosition': '-324px 0'
          });
           flag=true;  
        }
        
    });
    //歌词初始化
    var lrc="[00:00.00] 作曲 : 陈百强[00:01.00] 作词 : 郑国江[00:13.798] 愁绪挥不去苦闷散不去[00:20.799] 为何我心一片空虚[00:26.799]感情已失去一切都失去[00:31.800] 满腔恨愁不可消除[00:38.800]为何你的嘴里总是那一句[00:44.800] 为何我的心不会死[00:50.800]明白到爱失去一切都不对[00:56.800] 我又为何偏偏喜欢你 [01:01.800] 爱已是负累 相爱似受罪* [01:08.800]心底如今满苦泪[01:14.800] 旧日情如醉 此际怕再追[01:21.800] 偏偏痴心想见你[01:26.800] 为何我心分秒想着过去[01:32.800]为何你一点都不记起[01:38.800] 情义已失去恩爱都失去[01:44.800] 我却为何偏偏喜欢你[02:14.798] 爱已是负累 相爱似受罪*[02:20.798] 心底如今满苦泪[02:26.798] 旧日情如醉 此际怕再追[02:32.798] 偏偏痴心想见你[02:37.798] 为何我心分秒想着过去[02:43.798] 为何你一点都不记起[02:49.798] 情义已失去恩爱都失去[02:56.798] 我却为何偏偏喜欢你";
    function loadlrc(){
        var arr=lrc.split('[');
        var lrcs='<p>'+$('#name').text()+'</p><p>演唱：'+$('#artists').text()+'</p><br/>';
        for(var i=0;i<arr.length;i++){
            if(arr[i]){
                var time=arr[i].split(']')[0];
                var timer=time.split(':');
                timer=timer[0]*60+parseInt(timer[1]);
                lrcs+='<p id='+timer+'>'+arr[i].split(']')[1]+'</p>'; 
            }
    
            }
            $('.fm-lrc').html('<div id="lrcarea">'+lrcs+'</div>');

    }
    loadlrc();
    //时间显示及歌词控制
    $('#mp3')[0].ontimeupdate=function(){
         var duration=$('#mp3')[0].duration;
         var dminute='00';
         var dsecond='00';
         if(!isNaN(parseInt(duration/60))){
            dminute='0'+parseInt(duration/60);
            dsecond=parseInt(duration%60);
         //alert(dsecond);
            if(dsecond.toString().length==1){
                dsecond='0'+dsecond;
            }
         }
         
         var currentTime=parseInt($('#mp3')[0].currentTime);
         var second=parseInt(currentTime%60);
         if(second.toString().length==1){
            second='0'+second;
         }
         $('.totalTime').text(dminute+':'+dsecond);
         $('.curTime').text('0'+parseInt(currentTime/60)+':'+second);
         $('.progress').css({
            'width':parseInt(currentTime/duration*100)+'%'
         })

         if($('#'+currentTime+'')){
         //console.log($('#lrcarea p').index($('#'+currentTime+''))+"--"+$('#'+currentTime+'').text()+"--"+$('#'+currentTime+'').height());
             var pindex=$('#lrcarea p').index($('#'+currentTime+''));
             var hoffset=0;
             if(pindex>=0){
                for(var i=0;i<=pindex;i++){
                   hoffset+=$('#lrcarea p').eq(i).height();
                }
                $('#'+currentTime+'').addClass('curr').siblings().removeClass('curr').parent().animate({  
                'marginTop' : 200-hoffset
                });

             }
   
        }

    }
    function search(keywords){
        if(!$('.fm-love').is(':hidden')){
           $('.fm-header .love').click(); 
        }
        
        if(keywords!=''){
            $.ajax({
                method:'post',
                url:apiUrl.getMusic,
                data:{key:keywords},
                dataType: "json",
                success:function(data){
                    var res='';
                    for(var i=0;i<data.length;i++){
                        res+='<p class="res">'+data[i].artists+'-'+data[i].name+'</p>';
                    }
                    $('.fm-result').html(res).slideDown(500);
                    $('.res').click(function(){
                        


                        $('.fm-result').slideUp(500);
                        var k=$(this).index();
                        $('.fm-player img').attr('src',data[k].picUrl);
                        $('#name').text(data[k].name);
                        $('#name').attr('mid',data[k].id);

                        var mu_arr=$.cookie('musicid').split(',');
                        var eq=$.inArray($('#name').attr('mid'),mu_arr);
                        if(eq>=0){
                            currentIndex=eq;
                        }


                        
                        var album='《'+data[k].album+'》';
                        if(album.length>30){
                            album=album.substr(0,30)+'...';
                        }
                        $('#album').text(album);
                        $('#album').attr('alid',data[k].alid);
                        $('#artists').text(data[k].artists);
                        
                        //alert(data[k].mp3Url);
                        $('#mp3').attr('src',data[k].mp3Url);
                        $('#mp3')[0].play();
                        $('.fm-play').css({
                            'backgroundPosition': '-444px 0'
                        });
                        flag=false;
                        $('.fm-play-panel ul .fm-favor').css({
                                    'backgroundPosition':'-396px 0'
                                });
                        var mu_arr=$.cookie('musicid').split(',');
                        for(var i=0;i<mu_arr.length;i++){
                            if(mu_arr[i]==$('#name').attr('mid')){
                                $('.fm-play-panel ul .fm-favor').css({
                                    'backgroundPosition':'-636px 0'
                                });
                            }

                        }

                        if(data[k].mvid){
                            $('.fm-player .fm-operation .fm-mv').css("background-image","url("+img+"/images/mv.png)").attr('mvid',data[k].mvid);

                        }else{
                            $('.fm-player .fm-operation .fm-mv').css("background-image","url("+img+"/images/nomv.png)").removeAttr('mvid');
                        }
                        $.ajax({
                            method:'post',
                            url:apiUrl.getMusic,
                            data:{id:data[k].id},
                            //dataType: "json",
                            success:function(lrcs){
                                //alert(lrc);
                                lrc=lrcs;
                                loadlrc();
                            }
                        });



                     });
                   
                }
            });
        }
    }
    //歌曲搜索
    $('.search input[type=button]').click(function(){
        var keywords=$('.search input[type=text]').val();
        search(keywords);
    });

    $('.search input[type=text]').keydown(function(e){
        if(e.keyCode==13){
            $('.search input[type=button]').click();
        }
    });
    //歌名
    $('#name').click(function(){
        var keywords=$(this).text();
        search(keywords);
    });
    //歌手
    $('#artists').click(function(){
        var keywords=$(this).text();
        search(keywords);
    });
    //专辑
    $('#album').click(function(){
        if(!$('.fm-love').is(':hidden')){
           $('.fm-header .love').click(); 
        }
        var alids=$(this).attr('alid');
        $.ajax({
            method:'post',
            url:apiUrl.getAlbum,
            data:{alid:alids},
            dataType: "json",
            success:function(data){
                var res='';
                for(var i=0;i<data.length;i++){
                    res+='<p class="res">'+data[i].artists+'-'+data[i].name+'</p>';
                }
                $('.fm-result').html(res).slideDown(500);
                $('.res').click(function(){
                    $('.fm-result').slideUp(500);
                    var k=$(this).index();
                    $('.fm-player img').attr('src',data[k].picUrl);
                    $('#name').text(data[k].name);
                    $('#name').attr('mid',data[k].id);
                    var mu_arr=$.cookie('musicid').split(',');
                        var eq=$.inArray($('#name').attr('mid'),mu_arr);
                        if(eq>=0){
                            currentIndex=eq;
                        }
                    var album='《'+data[k].album+'》';
                    if(album.length>30){
                        album=album.substr(0,30)+'...';
                    }
                    $('#album').text(album);
                    $('#album').attr('alid',data[k].alid);
                    $('#artists').text(data[k].artists);
                    
                    //alert(data[k].mp3Url);
                    $('#mp3').attr('src',data[k].mp3Url);
                    $('#mp3')[0].play();
                    $('.fm-play').css({
                        'backgroundPosition': '-444px 0'
                    });
                    flag=false;
                    $('.fm-play-panel ul .fm-favor').css({
                                    'backgroundPosition':'-396px 0'
                                });
                        var mu_arr=$.cookie('musicid').split(',');
                        for(var i=0;i<mu_arr.length;i++){
                            if(mu_arr[i]==$('#name').attr('mid')){
                                $('.fm-play-panel ul .fm-favor').css({
                                    'backgroundPosition':'-636px 0'
                                });
                            }

                        }
                    if(data[k].mvid){
                        $('.fm-player .fm-operation .fm-mv').css("background-image","url("+img+"/images/mv.png)").attr('mvid',data[k].mvid);

                    }else{
                        $('.fm-player .fm-operation .fm-mv').css("background-image","url("+img+"/images/nomv.png)").removeAttr('mvid');
                    }
                    $.ajax({
                        method:'post',
                        url:apiUrl.getMusic,
                        data:{id:data[k].id},
                        //dataType: "json",
                        success:function(lrcs){
                            //alert(lrc);
                            lrc=lrcs;
                            loadlrc();
                        }
                    });

                 });
               
            }
        });
    })
    //音量控制 
    $('#fmVol .fm-vol-slider').click(function(ev){
        $('#mp3')[0].volume=ev.offsetX/50;
        $('#fmVol .fm-vol-slider .ui-slider-range').animate({
            width:ev.offsetX,
        },300);

    });
    //mv控制
    $('.fm-player .fm-operation .fm-mv').click(function(){
        if($(this).attr('mvid')){
            var mvid=$(this).attr('mvid');
            $.ajax({
                method:'post',
                url:apiUrl.getMv,
                data:{id:mvid},
                dataType: "json",
                success:function(data){
                    //console.log(data);
                    var op='';
                    for(var i in data){
                        op+='<p alt="'+data[i]+'" title="'+i+'p">'+i+'p</p>';
                    }
                    $('#opt').html(op).slideDown().children().click(function(){
                        $('#opt').slideUp();
                        var mp4Url=$(this).attr('alt');
                        $('#mp3')[0].pause();
                        $('.fm-play').css({
                        'backgroundPosition': '-324px 0'
                        });
                        flag=true;
                        $('#popmp4').append('<video id="mp4" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="none" data-setup="{}"><source src="" type="video/mp4"/></video>');
                        $('#mp4 source').attr('src',mp4Url);
						videojs('mp4', {}, function(){
							// Player (this) is initialized and ready.
						});
                        var maskwidth=$(document).width();
                        var maskheight=$(document).height();
                        $('#mask').css({
                            'width':maskwidth,
                            'height':maskheight
                        }).fadeIn(1000);
                        $('#popmp4').fadeIn(1000);
                    });                   
                }
            });
        }
    });
    $('#popmp4 .close').click(function(){
        //$('button.vjs-play-control').click();
        $('#mp4').remove();
        $('#mask').fadeOut(800);
        $('#popmp4').fadeOut(400);
    })
    $(document).click(function(){
        $('#opt').slideUp();
    });
    //mv播放器定位
    $(window).resize(function(){
        //alert($(this).width());
        var toleft=($(this).width()-$('#popmp4').width())/2;
        var totop=($(this).height()-$('#popmp4').height())/2;
        $('#popmp4').css({
            'left':toleft,
            'top':totop
        });
    });
    //播放进度控制
    $('.progressbar').click(function(e){
        $('#mp3')[0].currentTime=e.offsetX/$(this).width()*$('#mp3')[0].duration;
    });

  
    //自动播放
     $('#mp3')[0].onended=function(){
        $('.fm-play').css({
            'backgroundPosition': '-324px 0'
        });
        flag=true;  
        $('.fm-play-panel ul .fm-next').click();
     }







    $('.fm-play').css({
            'backgroundPosition': '-324px 0'
          });
    //cookie播放
    function cookieplay(cid){
        $.ajax({
            method:'post',
            url:apiUrl.cookieMusic,
            data:{mid:cid},
            dataType: "json",
            success:function(data){
                console.log(data);
                $('.fm-player img').attr('src',data.picUrl);
                $('#name').text(data.name);
                $('#name').attr('mid',data.id);
                var album='《'+data.album+'》';
                if(album.length>30){
                    album=album.substr(0,30)+'...';
                }
                $('#album').text(album);
                $('#album').attr('alid',data.alid);
                $('#artists').text(data.artists);
                
                //alert(data[k].mp3Url);
                $('#mp3').attr('src',data.mp3Url);
                $('#mp3')[0].play();
                $('.fm-play').css({
                    'backgroundPosition': '-444px 0'
                });
                flag=false;
                $('.fm-play-panel ul .fm-favor').css({
                    'backgroundPosition':'-636px 0'
                });
                if(data.mvid){
                    $('.fm-player .fm-operation .fm-mv').css("background-image","url("+img+"/images/mv.png)").attr('mvid',data.mvid);

                }else{
                    $('.fm-player .fm-operation .fm-mv').css("background-image","url("+img+"/images/nomv.png)").removeAttr('mvid');
                }
                $.ajax({
                    method:'post',
                    url:apiUrl.getMusic,
                    data:{id:data.id},
                    //dataType: "json",
                    success:function(lrcs){
                        //alert(lrc);
                        lrc=lrcs;
                        loadlrc();
                    }
                });
            }     
        });
    }
    
    if(!$.cookie('musicid')){
        $.cookie('musicid','',{expires :7});
        $.cookie('musicname','',{expires :7});
        $('.fm-play').click();
    }else{
        var list=$.cookie('musicid').split(',');
        //alert(list[0]);
        // for(var i=0;i<mu_arr.length;i++){
        //     if(mu_arr[i]=='66476'){
        //         $('.fm-play-panel ul .fm-favor').css({
        //             'backgroundPosition':'-636px 0'
        //         }) ;
        //     }

        // }
        // for(var i=0;i<list.length;){

        // alert(1);           
        // $('#mp3')[0].onended=function(){
        //         i=i+1;
        //     };
        // }
        if($.cookie('lastplay')){
            currentIndex=$.cookie('lastplay');
            cookieplay(list[currentIndex]);
        }else{
            cookieplay(list[1]);
        }
        
        

    }
    $('.fm-play-panel ul .fm-favor').click(function(){
        var mu_arr=$.cookie('musicid').split(',');
        var na_arr=$.cookie('musicname').split(',');
        // alert(mu_arr.length);
        // alert($('#name').attr('mid'));
        var flag=true;
        for(var i=0;i<mu_arr.length;i++){
            if(mu_arr[i]==$('#name').attr('mid')){
                mu_arr.splice(i,1);
                na_arr.splice(i,1);
                currentIndex--;
                $('.fm-play-panel ul .fm-favor').css({
                    'backgroundPosition':'-396px 0'
                });
                
                flag=false;
            }

        }
        if(flag){
            //mu_arr.push($('#name').attr('mid'));
            mu_arr.splice( currentIndex+1, 0, $('#name').attr('mid'));
            na_arr.splice( currentIndex+1, 0, $('#artists').text()+'-'+$('#name').text());
            currentIndex++;
            $('.fm-play-panel ul .fm-favor').css({
                    'backgroundPosition':'-636px 0'
                }) ;
            // alert(currentIndex);
            //  $('.fm-love p').eq(currentIndex-1).addClass('playcurr').siblings().removeClass('playcurr');

        }

        $.cookie('musicid',mu_arr.join(','),{expires :7});
        $.cookie('musicname',na_arr.join(','),{expires :7});
        getlist();
        $('.fm-love p').eq(currentIndex-1).addClass('playcurr').siblings().removeClass('playcurr');
        //$('.fm-header .love').click();
        $('.fm-header .love').click();
        $('.fm-header .love').click();

    });

      //播放下一曲
    

    
    $('.fm-play-panel ul .fm-next').click(function(){
        if($.cookie('musicid')!=''){
            var list=$.cookie('musicid').split(',');
            if(currentIndex==(list.length-1)){
                currentIndex=1;
            }else if(currentIndex==0){
                currentIndex=1;
            }else{
                currentIndex++;
            }
            if(!list[currentIndex]){
                currentIndex=1;
            }
            cookieplay(list[currentIndex]);
            
            
        }
        //alert(list[currentIndex]);
        //alert(currentIndex);
        
    });
    

    //更换背景图
    $('.fm-header .backimg').click(function(){
       $('.cont').slideToggle();
       
    })


    $('.cont input').click(function(){
        imgIndex=$(this).index();
        $('body').css({
            'background-image':'url('+$(this).attr('src')+')'
        });
        $('.cont').slideUp();
    });
    $('.cont p').click(function(){
        $('.cont').slideUp();
    });
    //自动更换背景(60")
    var imglength=$('.cont input').length;
    setInterval(function(){
        var nextIndex=imgIndex+1;
        if(imgIndex==imglength-1){
            nextIndex=0;
        }
        $('.cont input').eq(nextIndex).click();
    },60000);

    //收藏列表
    //获取cookie歌曲
    function getlist(){
        var playlist=$.cookie('musicname').split(',');
        var musiclist='';
        for(var i=1;i<playlist.length;i++){
            musiclist+='<p>'+i+'.'+playlist[i]+'</p>';
        }
        //alert('可以的');
        $('.fm-love').html(musiclist);
    }
    //显示列表
    $('.fm-header .love').click(function(){
        if(!$('.fm-result').is(':hidden')){
            $('.fm-result').slideUp(500);
        }
        getlist();
        var mu_arr=$.cookie('musicid').split(',');
        var eq=$.inArray($('#name').attr('mid'),mu_arr);
        if(eq>=0){
            $('.fm-love p').eq(currentIndex-1).addClass('playcurr').siblings().removeClass('playcurr');
        }


        
        $('.fm-love').fadeToggle(600);
        $('.fm-love p').click(function(){
            $(this).addClass('playcurr').siblings().removeClass('playcurr');
            var list=$.cookie('musicid').split(',');
            currentIndex=$(this).index()+1;
            cookieplay(list[currentIndex]);
        });
        $('#mp3')[0].onloadedmetadata=function(){
            var mu_arr=$.cookie('musicid').split(',');
            if($.inArray($('#name').attr('mid'),mu_arr)<0){
                $('.fm-love p').removeClass('playcurr');
            }else{
                $('.fm-love p').eq(currentIndex-1).addClass('playcurr').siblings().removeClass('playcurr');
            }
            $.cookie('lastplay',currentIndex,{expires :7});
        } 



    });

   //二维码控制
   $('#share').hover(function(){
        $('#weixin').fadeIn(800);

   },function(){
        $('#weixin').fadeOut(800);

   });
});