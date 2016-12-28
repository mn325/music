$(function() {
    //提交留言
    $('.weui_btn_primary').on('click',function(){
        var content=$('.weui_textarea').val();
        var arr=content.match(/^\s*(.*?)\s*$/);
        content=arr[1];
        if(content==''){//未输入提示
            $('#dialog').show();
        }else{//提交至服务器
            $.ajax({
                url:url,
                method:'post',
                data:{
                    content:content
                },
                dataType:'json',
                beforeSend:function(){
                    $('#loadingToast').show();
                },
                success:function(data){
                    $('#loadingToast').hide();
                    if(data){
                        $('#toast').show();
                        $('.weui_textarea').val('');
                        //附加留言
                        var contents='<div class="weui_panel"><div class="weui_panel_hd">来自'+data.address+'的留言</div><div class="weui_panel_bd"><div class="weui_media_box weui_media_text"><p class="weui_media_desc">'+data.content+'</p><ul class="weui_media_info"><li class="weui_media_info_meta">留言板</li><li class="weui_media_info_meta">3秒前</li><li class="weui_media_info_meta weui_media_info_meta_extra">'+data.address+'</li></ul></div></div></div>';
                        $('h1.weui_article').after(contents);
                        setTimeout(function(){
                            $('#toast').hide();
                        },1500);
                    }
                    
                }
            })
        }
   });
   //关闭未输入留言内容提示
    $('a.weui_btn_dialog').on('click',function(){
        $('#dialog').hide();
   }); 
});