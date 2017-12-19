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
              url:'?r=terminus/last-edit',
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
    	var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择打印订单');
            return false;
        }
        
        $.ajax({
             type: "post",
             url:'?r=terminus/goods-print-other',
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
    	var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择打印订单');
            return false;
        }
        $.ajax({
             type: "post",
             url:'?r=terminus/goods-print-other',
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
    	var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择打印订单');
            return false;
        }
        $.ajax({
            type: "post",
            url:'?r=terminus/ajax-all-over',
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
    
})