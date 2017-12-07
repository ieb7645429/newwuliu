$(function(){
    $('.operation').click(function(){
        var getObj = $(this);
        var orderId = getObj.data('orderId');
        var data = {
            'order_id':getObj.data('orderId')
        };
        $.ajax({
             type: "post",
             url:'?r=dl/return-complete/return-order-edit',
             data:data,
             async:true,
             success:function(data){
             var obj = $.parseJSON(data);
                if(obj.error==0){
                    alert(obj.message);
	                $('.table-tr-'+orderId).remove();
                 }else if(obj.error==1){
                    alert(obj.message);
                 }
              }
            })
        })	
})