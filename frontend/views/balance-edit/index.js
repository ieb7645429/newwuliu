$(function(){
	$('.operation').click(function(){
		var order_id = $(this).data('orderId');
		console.log(order_id);
		$('#order_id').val(order_id);
	})
	//删除备注提交
	$('#confirm').click(function(){
		if($('#delContent').val()==''){
			alert('备注信息不能为空');
			return false;
		}
		if(!confirm("是否确定删除订单？")){
     		return false;
     	}
        var getObj = $(this);
        var orderId = $('#order_id').val();
        var data = {
            'order_id':orderId,
            'del_content':$('#delContent').val()
        };
        getObj.attr('disabled','disabled');
        $.ajax({
             type: "post",
             url:'?r=balance-edit/del-content',
             data:data,
             dataType:'json',
             async:true,
             success:function(data){
            	if(data.code==200){
            		location.href = data.url;
            	}
            }
        })
	})
	
	
	
})