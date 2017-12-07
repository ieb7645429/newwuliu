$(function(){
$('.operation').click(function(){
		if(!confirm("是否确定收货？")){
			return false;
		}
         var getObj = $(this);
         var orderId = getObj.data('orderId');
         var data = {
             'order_id':getObj.data('orderId')
         };
         $.ajax({
              type: "post",
              url:'?r=dl/driver/order-edit',
              data:data,
              async:true,
              success:function(data){
              var obj = $.parseJSON(data);
                 if(obj.error==0){
                  alert(obj.message);
                  $('.goodsTableTr_'+orderId).remove();
                }else if(obj.error==1){
                  alert(obj.message);
                }
               }
             })
     })
     /**
     * 打印送货单
     */
    $('.js-print').click(function(){
    	if(!confirm("是否选择打印送货单？")){
    		return false;
    	}
    	var getObj = $(this);
        var chk_value =[];
        $('input[name="print"]:checked').each(function(){
            chk_value.push($(this).val()); 
        });
        if(chk_value.length==0){
            alert('请选择打印订单');
            return false;
        }
        getObj.attr('disabled','disabled');
        var data = {
            'order_sn':$('.orderSn').val(),
            'goods_sn':$('.goodsSn').val(),
            'order_arr':chk_value
            
        };
        $.ajax({
             type: "post",
             url:'?r=dl/driver/city-wide-print',
             data:data,
             async:true,
             success:function(data){
            	 getObj.attr('disabled',false);
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
					printCounterfoil(obj.data,2);
                    console.log(obj.data);
                }
            }
      });
    })
    /**
     * 打印码单
     */
    $('.js-print-memo').click(function(){
    	if(!confirm("是否选择打印码单？")){
    		return false;
    	}
    	var getObj = $(this);
        var chk_value =[];
        $('input[name="print"]:checked').each(function(){
            chk_value.push($(this).val()); 
        });
        if(chk_value.length==0){
            alert('请选择打印订单');
            return false;
        }
        getObj.attr('disabled','disabled');
        var data = {
            'order_sn':$('.orderSn').val(),
            'goods_sn':$('.goodsSn').val(),
            'order_arr':chk_value
            
        };
        $.ajax({
             type: "post",
             url:'?r=dl/driver/city-wide-print',
             data:data,
             async:true,
             success:function(data){
            	 getObj.attr('disabled',false);
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
                	printreceipt(obj.data);
                    console.log(obj.data);
                }
            }
      });
    })
     
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
})