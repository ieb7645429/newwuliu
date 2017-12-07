$(function(){
    $('.operation').click(function(){
        var getObj = $(this);
        var orderId = getObj.data('orderId');
        var data = {
            'goods_id':getObj.data('goodsId'),
            'user_id':$('#driver-driver_id').val()
        };
        $.ajax({
             type: "post",
             url:'?r=dl/driver-manager/goods-edit',
             data:data,
             async:true,
             success:function(data){
               var obj = $.parseJSON(data);
                if(obj.error==0){
                	alert(obj.message);
                	location.replace(location.href);
                 }
              }
            });
        
        });
        
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
        
});