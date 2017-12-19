$(function(){
	var logistics_sn = $("input[name='LogisticsReturnOrderSearch[logistics_sn]']");
	if(logistics_sn.val()){
		logistics_sn.focus().select();
	}
	
	
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
 $('#searchButton').click(function(){
	 $("#download_type").val('0');
     $('#w0').attr('target', "_self");
     $('#w0').submit();
 })
 $('#downloadExcel').click(function(){
	 $("#download_type").val('1');
     $('#w0').attr('target', "_blank");
     $('#w0').submit();
 })
})