$(function(){
	getCreateType($('#count').val());
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
		$(this).attr('disabled','disabled');
		$.ajax({
            type: 'post',
            url:'?r=member-pay/with-drawal',
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
	$('.select-on-check-all').on('change',function(){
		var order_arr =[];
		 $('.checkbox').each(function(){
             order_arr.push($(this).val()); 
         });
		goCookie($(this),order_arr);
	})
	
	$('.checkbox').on('change',function(){
		goCookie($(this),[$(this).val()]);
	})
	
	function goCookie(obj,order_arr){
		
		if(obj.is(':checked')){
			var url = '?r=member-pay/add-cookie';
		}else{
			var url = '?r=member-pay/del-cookie';
		}
		$.ajax({
            type: 'post',
            url:url,
            data:{order_arr:order_arr},
            dataType:'json',
            async:false,
            success:function(data){
            	console.log(data);
	              $('#count').val(data);
	              getCreateType(data);
             }
       })
	}
	
	$(document).on('click', '#create', function () {
        var count = $('#count').val();
        if(count>0){
            $('#confirm').prop('disabled',false);
            $.ajax({
                type: 'post',
                url:'?r=member-pay/get-total',
                dataType:'json',
                async:false,
                success:function(data){
                    $('#money').attr({'value':data});
                 }
            })
            
        }else{
            alert('请选择提现订单');
            $('#money').attr({'value':'0'});
            $('#confirm').prop('disabled',true);
            return false;
        }
    });
	
	function getCreateType($count){
		if($count>0){
            $('#create').attr({'data-target':'#create-modal'});
         }else{
            $('#create').attr({'data-target':'#create-null'});
         }
	}
})