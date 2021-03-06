$(function(){
    /**
     * order状态修改
     * 靳健
     */
     $('.operation').click(function(){
    	 if(!confirm("是否确定订单完成？")){
     		return false;
     	} 
         var getObj = $(this);
         var orderId = getObj.data('orderId');
         var data = {
             'order_id':getObj.data('orderId')
         };
         $.ajax({
              type: "post",
              url:'?r=dl/terminus/last-edit',
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
    /**
     * 落地点打印
     * 靳健
     */
    $('.js-print-other').click(function(){
    	if(!confirm("是否选择打印？")){
     		return false;
     	} 
        var chk_value =[];
        $('input[name="print"]:checked').each(function(){
            chk_value.push($(this).val()); 
        });
        if(chk_value.length==0){
            alert('请选择打印订单');
            return false;
        }
        var data = {
            'order_sn':$('.orderSn').val(),
            'goods_sn':$('.goodsSn').val(),
            'order_arr':chk_value
            
        };
        $.ajax({
             type: "post",
             url:'?r=dl/terminus/goods-print-other',
             data:data,
             async:true,
             success:function(data){
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
                	console.log(obj.data);
                    $.each(obj.data, function(i, item) {
                        $('#print_log_' + item.order_id).html('已打印');
                    });
                    printCounterfoil(obj.data);
                }
            }
      });
    })
	//增加单独打印正面凭条
	//printCounterfoil_Z
	 $('.js-print-other-z').click(function(){
    	if(!confirm("是否选择打印？")){
     		return false;
     	} 
        var chk_value =[];
        $('input[name="print"]:checked').each(function(){
            chk_value.push($(this).val()); 
        });
        if(chk_value.length==0){
            alert('请选择打印订单');
            return false;
        }
        var data = {
            'order_sn':$('.orderSn').val(),
            'goods_sn':$('.goodsSn').val(),
            'order_arr':chk_value
            
        };
        $.ajax({
             type: "post",
             url:'?r=dl/terminus/goods-print-other',
             data:data,
             async:true,
             success:function(data){
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
                	console.log(obj.data);
                    $.each(obj.data, function(i, item) {
                        $('#print_log_' + item.order_id).html('已打印');
                    });
                    printCounterfoil_Z(obj.data);
                }
            }
      });
    })
    $('#all-over').click(function(){
    	if(!confirm("是否选择批量完成？")){
     		return false;
     	} 
    	var chk_value =[];
        $('input[name="print"]:checked').each(function(){
            chk_value.push($(this).val()); 
        });
        if(chk_value.length==0){
            alert('请选择打印订单');
            return false;
        }
        var data = {
            'order_arr':chk_value
                
        };
        $.ajax({
            type: "post",
            url:'?r=dl/terminus/ajax-all-over',
            data:data,
            async:true,
            success:function(data){
               var obj = $.parseJSON(data);
               if(obj.error==1){
                  alert(obj.message);
               }else{
            	   alert(obj.message);
            	   location.replace(location.href);
            	   
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