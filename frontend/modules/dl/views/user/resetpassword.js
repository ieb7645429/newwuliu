$(function(){
        $('#phone').blur(function(){ 
		   var phone = $('#phone').val().replace(/^\s+|\s+$/g,"");
		   if(phone.length<11){
			   $("#phone").attr('style','border:1px solid #a94442');
			   $("#phone-label").attr('style','color:#a94442');
			   $("#phone-p").text('电话号码错误');
			   $("#phone").val('');
		       return;
		   }      
            var data = {
                'phone':phone,
            };
            $.ajax({
                 type: "post",
                 url:'?r=dl/user/check-uid',
                 data:data,
                 async:false,
				 dataType:"text",
                 success:function(data){
                    if(data == false){
					   $("#phone").attr('style','border:1px solid #a94442');
					   $("#phone-label").attr('style','color:#a94442');
					   $("#phone-p").text('电话号码错误');
					   $("#phone").val('');
		    	      $('#changepwd').attr('disabled',true);
                    }
					else{
					 $('#changepwd').attr('disabled',false);
					 $("#phone").attr('style','');
					   $("#phone-label").attr('style','');
					   $("#phone-p").text('');
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
    if($('#newpwd').val().length==0 || $('#resetpasswordform-password').val().length==0 || $("#phone").val().length<11)
	{ return false;}
	else{ return true;}
}