$(function(){
    $('.operation').click(function(){
        var getObj = $(this);
        var orderId = getObj.data('orderId');
        var data = {
            'goods_id':getObj.data('goodsId')
        };
        $.ajax({
             type: "post",
             url:'?r=dl/driver/goods-edit',
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
        $('.js-print').click(function(){
        	if(!confirm("是否选择打印？")){
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
                    getObj.attr('disabled',false);
                    var obj = $.parseJSON(data);
                    if(obj.error==1){
                       console.log(obj.message);
                    }else{
                       console.log(obj.data);
                    	printreceipt(obj.data);
                    }
                }
          });
        });
        $('.js-loading-print').click(function(){
        	if(!confirm("是否选择封车打印？")){
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
                'loading':1
            };
            $.ajax({
                 type: "post",
                 url:'?r=dl/driver/goods-print',
                 data:data,
                 async:true,
                 dataType:'json',
                 success:function(data){
                    getObj.attr('disabled',false);
                    if(data.error==1){
                       console.log(data.message);
                       location.replace(location.href);
                    }else{
                        printreceipt(data.data);
                       console.log(data.data);
                        //location.replace(location.href);
                    }
                }
          });
        });
        /**
         * 批量扫码
         */
        $('.js-goods-submit').click(function(){
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
                };
            $.ajax({
                type: "post",
                url:'?r=dl/driver/ajax-batch-scan',
                data:data,
                async:true,
                dataType:'json',
                success:function(data){
                	console.log(data);
                	getObj.attr('disabled',false);
                	if(data.error==1){
                        alert(data.message);
                     }else{
                        alert(data.message);
                        location.replace(location.href);
                     }
               }
            });
        	
        	
        })
        
        
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
                };
            $.ajax({
                type: "post",
                url:'?r=dl/driver/order-manage',
                data:data,
                async:true,
                success:function(data){
                	getObj.attr('disabled',false);
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
		   //计算选中数量
		   CountCheckBox();
     })
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
});