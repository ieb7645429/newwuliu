$(function(){
	$('.operation').click(function(){
		var order_id = $(this).data('orderId');
		setTimeout(function(){
			$('#sender').focus();
		},500);
		console.log(order_id);
		$('#order_id').val(order_id);
	})
	$('#sender').keydown(function(event){ //回车键
		if(event.keyCode==13){ 
		$("#confirm").click(); 
		}
	})
	$('#confirm').click(function(){
		if($('#sender').val()==''){
			alert('送货人不能为空');
			return false;
		}
		if(!confirm("是否确定送货处理？")){
     		return false;
     	}
        var getObj = $(this);
        var orderId = $('#order_id').val();
        var data = {
            'order_id':orderId,
            'sender':$('#sender').val()
        };
        getObj.attr('disabled','disabled');
        $.ajax({
             type: "post",
             url:'?r=return-complete/return-order-edit',
             data:data,
             async:true,
             success:function(data){
        	 getObj.attr('disabled',false);
             var obj = $.parseJSON(data);
                if(obj.error==0){
                alert(obj.message);
                $('.tr-order-'+orderId).remove();
                location.replace(location.href);
                 }else if(obj.error==1){
                    alert(obj.message);
                 }
              }
            })
	})
    /**
     * 退货打印
     */
    $('.js-print').click(function(){
    	if(!confirm("是否选择打印？")){
     		return false;
     	}
    	var getObj = $(this);
    	var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择打印订单');
            return false;
        }
        getObj.attr('disabled','disabled');
        $.ajax({
             type: "post",
             url:'?r=return-complete/return-print',
             async:true,
             success:function(data){
            	 getObj.attr('disabled',false);
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
                	console.log(obj.data);
					printCounterfoil(obj.data,1);
                    //console.log(obj.data);
                }
            }
      });
    })
    $('#check_all').change(function(){
		 if($('#check_all').is(':checked')){
	       $('.order_check').each(function(){
	         if(!$(this).prop('disabled')){
	            $(this).prop('checked',true);
	         }
	       })
	     }else{
	       $('input[type=checkbox]').prop('checked',false);
	     }
	})
})