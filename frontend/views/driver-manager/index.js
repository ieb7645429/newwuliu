$(function(){
    $('.operation').click(function(){
        var getObj = $(this);
        var orderId = getObj.data('orderId');
        var data = {
            'goods_id':getObj.data('goodsId'),
            'user_id':$('#driver_id').val()
        };
        $.ajax({
             type: "post",
             url:'?r=driver-manager/goods-edit',
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
     
     //大司机 司机切换
     $('.driver-list').click(function(){
    	 var obj = $(this);
    	 var driver_id = obj.data('driverId');
    	 var data = {
    			 driver_id:driver_id,
    	 }
    	 obj.attr('disabled','disabled');
    	 $.ajax({
             type: "post",
             data:data,
             url:'?r=driver-manager/ajax-driver-change',
             data:data,
             dataType:'json',
             async:true,
             success:function(data){
                obj.attr('disabled',false);
                alert(data.message);
                location.replace(location.href);
              }
            });
        
        });
     /**
      * 批量提交封车
      */
     $('.js-submit').click(function(){
     	if(!confirm("是否选择提交订单？")){
     		return false;
     	}
     	var getObj = $(this);
         var chk_value =[];
         $('input[name="print"]:checked').each(function(){
             chk_value.push($(this).val()); 
         });
         if(chk_value.length==0){
             alert('请选择提交订单');
             return false;
         }
         getObj.attr('disabled','disabled');
         var data = {
                 'order_arr':chk_value,
                 'driver_id':$('#driver_id').val(),
             };
         $.ajax({
             type: "post",
             url:'?r=driver/order-manage',
             data:data,
             async:true,
             success:function(data){
             	getObj.attr('disabled',false);
                var obj = $.parseJSON(data);
                   alert(obj.message);
                   location.replace(location.href);
            }
         });
     	
     	
     })
        
});