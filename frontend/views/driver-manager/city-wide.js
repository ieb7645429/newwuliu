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
              url:'?r=driver/order-edit',
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
    	var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择提交订单');
            return false;
        }
        getObj.attr('disabled','disabled');
        $.ajax({
             type: "post",
             url:'?r=driver/city-wide-print',
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
     * 打印送货单(正)
     */
    $('.js-print-z').click(function(){
    	if(!confirm("是否选择打印送货单(正)？")){
    		return false;
    	}
    	var getObj = $(this);
    	var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择提交订单');
            return false;
        }
        getObj.attr('disabled',true);
        $.ajax({
             type: "post",
             url:'?r=driver/city-wide-print',
             async:true,
             success:function(data){
            	//两秒后执行          	 
            	setTimeout(function(testr){
            		getObj.attr('disabled',false);
            	},2000);
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
                    $.each(obj.data, function(i, item) {
                        $('#print_log_' + item.order_id).html('已打印');
                    });
					printCounterfoil_Z(obj.data,2);
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
             url:'?r=driver/city-wide-print',
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
    /**
     * 打印小码单
     */
    $('.js-small-print').click(function(){
    	var getObj = $(this);
    	if(!confirm("是否选择打印小码单？")){
    		return false;
    	}
    	var data = {
    		driver_id:$("#driver_id").val(),
    	}
    	getObj.attr('disabled','disabled');
    	$.ajax({
            type: "post",
            data:data,
            url:'?r=driver/small-print',
            dataType:'json',
            async:true,
            success:function(data){
//           	 getObj.attr('disabled',false);
            	//两秒后执行          	 
            	setTimeout(function(testr){
            		getObj.attr('disabled',false);
            	},2000);
             if(data.code==404)
            	 alert(data.message);
             if(data.code==200)//打印小码单
                 console.log(data.data);
                 printSmallReceipt(data.data);
           }
    	});
    	
    })
	
	//小码单是否全部打印修改
	$('#checkbox-input').click(function(){
		var obj = $(this);
    	obj.attr('disabled','disabled');
    	$.ajax({
            type: "post",
            url:'?r=driver/ajax-print-change',
            dataType:'json',
            async:true,
            success:function(data){
                obj.attr('disabled',false);
                if(data.code == 400){
                    alert(data.message);
                }
                if(data.code == 200){
                    alert(data.message);
                    if(data.status==1){
                        obj.attr('checked',true);
                    }
                    if(data.status==0){
                    	obj.attr('checked',false)
                    }
                }
           }
        });
	})
	//大司机 司机切换
    $('.driver-list').click(function(){
   	 var obj = $(this);
   	 var driver_id = obj.data('driverId');
   	 var rule = $('#rule').val();
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
               location.replace(window.location.pathname+'?r='+rule);
             }
           });
       
       });
})