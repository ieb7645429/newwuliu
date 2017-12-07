$(function(){
	var orderId = $("#tag_order_id").val();
	if($('#tag_print').val() == 1){
		setTimeout(function(){
			goPrint(orderId,'shouju');
		},1000);
	}
    $('.js-print').click(function(){
		if(!confirm("是否确定打印？")){
			return false;
		}
		goPrint(orderId,'shouju')
    })
    function goPrint(order_id,category){
    	var data = {
            'order_id':orderId,
        };
        $.ajax({
             type: "post",
             url:'?r=hlj/return/order-print',
             data:data,
             async:true,
             success:function(data){
                var obj = $.parseJSON(data);
                console.log(obj);
                //1退货状态
                if(category=='huotie'){
                    print_goods_tag(obj,1);
                 }
                 else{
                   printKD_HLJ(obj,obj[0]['return_type']);
                 }
            }
        })
    }
})