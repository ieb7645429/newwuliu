$(function(){
	var orderId = $("#tag_order_id").val();
	if($('#tag_print').val() == 1){
		$('.print-div').css({'display':'block'});
		setTimeout(function(){
			goPrint(orderId,'shouju');	
			setTimeout(function(){
				window.location.href = "?r=hlj/employee/create";
			},2000);
		},1000);
		setTimeout(function(){
			$('.print-div').css({'display':'none'});
		},2000);
	}
    $('.js-print').click(function(){
    	if(!confirm("是否确定打印？")){
    		return false;
    	} 
        goPrint(orderId,'huotie');
    })
	$('.js-print-kd').click(function(){
    	if(!confirm("是否确定打印？")){
    		return false;
    	} 
        goPrint(orderId,'shouju');
    })
    function goPrint($order_id,category){
		///console.log(category);
        var data = {
               'order_id':$order_id,
           };
           $.ajax({
                type: "post",
                url:'?r=hlj/employee/order-print',
                data:data,
                async:true,
                success:function(data){
                   var obj = $.parseJSON(data);
                   //console.log(obj);
                   console.log(obj);
				   if(category=='huotie'){
                      print_goods_tag(obj);
				   }
				   else{
				     printKD_HLJ(obj,3);
				   }
				 
               }
           })
   }

})

