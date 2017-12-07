$(function(){
    $('.confirm').on('click', function(){
        $('#amount').val($(this).data('amount'));
        $('#userId').val($(this).data('userId'));
        $('#money').val('');
    });
    
	var reg = new RegExp("^[0-9]*$");
	$(document).on('input propertychange','#money',function(){
	    var withdrawal_amount = $('#amount').val();
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
		var data = {
                'amount':parseInt($('#money').val()),
                'userId': $('#userId').val(),
            };
		$.ajax({
            type: 'post',
            url:'?r=balance-log/with-drawal',
            data:data,
            dataType:'json',
            async:true,
            success:function(data){
              if(data.error==0){
                alert(data.message);
                location.replace(location.href);
              }else {
                alert(data.message);
              }
             }
           })
	})
	
	
})