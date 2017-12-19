$(function(){
	$('#logisticsroute-logistics_route_id').change(function(){
        var data = {
            'route_id':$(this).val()
        };
        $.ajax({
             type: "post",
             url:'?r=driver-manager/ajax-get-driver-list',
             data:data,
             async:true,
             success:function(data){
                 $('#logisticsorder-driver_member_id option:not(:first)').remove();
                 $('#logisticsorder-driver_member_id').append(data);
              }
            })
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
        var data = {
            'order_sn':$('.orderSn').val(),
            'goods_sn':$('.goodsSn').val(),
        };
        
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