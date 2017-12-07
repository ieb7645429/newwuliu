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
             url:'?r=driver/city-wide-print',
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
})