$(function(){
    $('.operation').click(function(){
        var getObj = $(this);
        var orderId = getObj.data('orderId');
        var data = {
            'goods_id':getObj.data('goodsId')
        };
        $.ajax({
             type: "post",
             url:'?r=driver/goods-edit',
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
            var count = $('#count_js').val();
            if(count==0||count=='0'){
                alert('请选择提交订单');
                return false;
            }
            getObj.attr('disabled','disabled');
            $.ajax({
                 type: "post",
                 url:'?r=driver/goods-print',
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
                 url:'?r=driver/goods-print',
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
        	var count = $('#count_js').val();
            if(count==0||count=='0'){
                alert('请选择提交订单');
                return false;
            }
            getObj.attr('disabled','disabled');
            $.ajax({
                type: "post",
                url:'?r=driver/ajax-batch-scan',
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
        	var count = $('#count_js').val();
            if(count==0||count=='0'){
                alert('请选择提交订单');
                return false;
            }
            getObj.attr('disabled','disabled');
            $.ajax({
                type: "post",
                url:'?r=driver/order-manage',
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
});