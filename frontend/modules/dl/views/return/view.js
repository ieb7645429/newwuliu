$(function(){
	var orderId = $("#tag_order_id").val();
	if($('#tag_print').val() == 1){
		setTimeout(function(){
			goPrint(orderId);
		},1000);
	}
    $('.js-print').click(function(){
		if(!confirm("是否确定打印？")){
			return false;
		}
		goPrint(orderId)
    })
    function goPrint(order_id){
    	var data = {
            'order_id':orderId,
        };
        $.ajax({
             type: "post",
             url:'?r=dl/return/order-print',
             data:data,
             async:true,
             success:function(data){
                var obj = $.parseJSON(data);
                console.log(obj);
                print_goods_tag(obj,1);//1退货状态
            }
        })
    }
})