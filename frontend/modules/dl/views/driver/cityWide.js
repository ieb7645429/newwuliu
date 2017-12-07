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
                  var button = '<span class="finish">已确认</span>';
                  getObj.after(button);
                  getObj.remove();
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
            	//两秒后执行          	 
             	setTimeout(function(testr){
             		getObj.attr('disabled',false);
             	},2000);
//            	 getObj.attr('disabled',false);
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
                    $.each(obj.data, function(i, item) {
                        $('#print_log_' + item.order_id).html('已打印');
                    });
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
        var chk_value =[];
        $('input[name="print"]:checked').each(function(){
            chk_value.push($(this).val()); 
        });
        if(chk_value.length==0){
            alert('请选择打印订单');
            return false;
        }
        getObj.attr('disabled',true);
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
     * 打印送货单
     */
    $('.js-loading-print').click(function(){
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
            'order_arr':chk_value,
            'loading':0
        };
        $.ajax({
             type: "post",
             url:'?r=dl/driver/goods-print',
             data:data,
             async:true,
             success:function(data){
//            	 getObj.attr('disabled',false);
            	//两秒后执行          	 
             	setTimeout(function(testr){
             		getObj.attr('disabled',false);
             	},2000);
                 var obj = $.parseJSON(data);
                 if(obj.error==1){
                    console.log(obj.message);
                 }else{
                    console.log(obj.data);
                 	printreceipt(obj.data);
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
    	getObj.attr('disabled','disabled');
    	$.ajax({
            type: "post",
            url:'?r=dl/driver/small-print',
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
		 //计算选中数量
		   CountCheckBox();
		
	});
	//点击单个checkbox计算
	$('input[name=print]').change(function(){
	    CountCheckBox();
	});
	//统计checkbox选中数量
	function CountCheckBox(){
	 var count = 0;
	 $("input[name=print]").each(function(){
		 if($(this).is(':checked')){ 
			count+=1;
		 }
	 });
	 console.log(count);
	 $('#count').html(count);
	}
	
})