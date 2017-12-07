$(function(){
$('.operation').click(function(){
		var order_id = $(this).data('orderId');
		console.log(order_id);
		$('#order_id').val(order_id);
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
})