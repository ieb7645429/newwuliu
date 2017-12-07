$(function(){
	var withdrawal_amount = $('#withdrawal_amount').val();
	var reg = new RegExp("^[0-9]*$");
	$(document).on('input propertychange','#money',function(){
		if(!reg.test($(this).val())){
			$('.payPrompt').show();
			$('#confirm').prop('disabled',true);
			return false;
	    }else{
	    	$('.payPrompt').hide();
	    	$('#confirm').prop('disabled',false);
	    }
		if(parseInt($(this).val())>parseInt(withdrawal_amount)||$(this).val()==''||$(this).val()<=0){
			$('#confirm').prop('disabled',true);
		}else{
			$('#confirm').prop('disabled',false);
		}
	})
	$(document).on('click','#confirm',function(){
		if(!confirm("是否确定提现？")){
			return false;
		}
		var order_arr = [];
		$('input[name="order_arr[]"]:checked').each(function(){
            order_arr.push($(this).val()); 
        });
		var data = {
                'amount':parseInt($('#money').val()),
                'order_arr':order_arr,
            };
		$(this).attr('disabled','disabled');
		$.ajax({
            type: 'post',
            url:'?r=dl/member-pay/with-drawal',
            data:data,
            dataType:'json',
            async:true,
            success:function(data){
              if(data.error==0){
                alert(data.message);
                location.replace(location.href);
              }else if(data.error==1){
                alert(data.message);
                //location.replace(location.href);
              }
             }
           })
	})
	
	
})