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

    // $('#handle-button').click(function () {
    //
    //     var checkedArr = [];
    //     var i = 0;
    //     $('.order_check').each(function(){
    //
    //         if($(this).prop('checked')){
    //             checkedArr[i] = $(this).val();
    //             i ++ ;
    //         }
    //     });
    //
    //     console.log(checkedArr);
    // });
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
         var data = {
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
                   // alert(obj.message);
                   location.replace(location.href);
            }
         })
     	
     	
     })
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
        var data = {
            'user_id':$('#driver_id').val(),
        };
        $.ajax({
            type: "post",
            url:'?r=driver/ajax-batch-scan',
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
    //外阜司机打印
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
        
});