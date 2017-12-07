$(function(){
        $('#oldpwd').blur(function(){        
            var data = {
                'pwd':$('#oldpwd').val(),
            };
            $.ajax({
                 type: "post",
                 url:'?r=site/check-pwd',
                 data:data,
                 async:false,
				 dataType:"text",
                 success:function(data){
                    if(data == false){
					   $("#oldpwd").attr('style','border:1px solid #a94442');
					   $("#oldpwd-label").attr('style','color:#a94442');
					   $("#oldpwd-p").text('OldPassword错误');
					   $("#oldpwd").val('');
                    }
					else{
					 $('#changepwd').attr('disabled',false);
					}
                }
          });
        });
       $('#newpwd').blur(function(){        
            var npwd = $('#newpwd').val();
			var pwd  = $('#resetpasswordform-password').val();
			if(pwd!=npwd){
			    $("#newpwd").attr('style','border:1px solid #a94442');
				$("#newpwd-label").attr('style','color:#a94442');
				$("#newpwd-p").text('RePassword与第一次录入不符');
				$("#newpwd").val('');
		    }
			else if(npwd.length==0)
			{
				$("#newpwd").attr('style','border:1px solid #a94442');
				$("#newpwd-label").attr('style','color:#a94442');
				$("#newpwd-p").text('RePassword与第一次录入不符');
				$("#newpwd").val('');
			}
			else{
				$("#newpwd").attr('style','');
				$("#newpwd-label").attr('style','');
				$("#newpwd-p").text('');
			}
        }); 
});
function ok(){
  //console.log('hi');
  //检查是否为空
    if($('#newpwd').val().length==0 || $('#resetpasswordform-password').val().length==0 || $("#oldpwd").val().length==0 )
	{ return false;}
	else{ return true;}
}