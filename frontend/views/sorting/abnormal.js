$(function(){
    $('.operation').click(function(){
    	if(!confirm("是否选择恢复订单？")){
     		return false;
     	}
        var getObj = $(this);
        var data = {
            'order_id':getObj.data('orderId')
        };
        $.ajax({
             type: "post",
             url:'?r=sorting/recover-edit',
             data:data,
             async:true,
             success:function(data){
	                   var obj = $.parseJSON(data);
	                   console.log(obj);
	                   alert(obj.message);
	                   location.replace(location.href);
	              }
            })
        })
})