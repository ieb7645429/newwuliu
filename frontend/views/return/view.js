$(function(){
	var orderId = $("#tag_order_id").val();
	if($('#tag_print').val() == 1){
		setTimeout(function(){
			goPrint(orderId);
			goPrintSj(orderId);
		},1000);
	}
    $('.js-print').click(function(){
		if(!confirm("是否确定打印？")){
			return false;
		}
		goPrint(orderId)
    })
    $('.js-sj-print').click(function(){
		if(!confirm("是否确定打印收据？")){
			return false;
		}
		goPrintSj(orderId)
    })
    function goPrint(order_id){
    	var data = {
            'order_id':orderId,
        };
        $.ajax({
             type: "post",
             url:'?r=return/order-print',
             data:data,
             async:true,
             success:function(data){
                var obj = $.parseJSON(data);
                console.log(obj);
                print_goods_tag(obj,1);//1退货状态
            }
        })
    }
    function goPrintSj(order_id){
    	var data = {
            'order_id':orderId,
        };
        $.ajax({
             type: "post",
             url:'?r=return/order-print',
             data:data,
             async:true,
             success:function(data){
                var obj = $.parseJSON(data);
                console.log(obj);
                printCounterfoil(obj,1);//1退货状态
            }
        })
    }
})