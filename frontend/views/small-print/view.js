$(function(){
	$('.small-print').click(function(){
		var getObj = $(this);
    	if(!confirm("是否选择打印小码单？")){
    		return false;
    	}
    	getObj.attr('disabled','disabled');
    	$.ajax({
            type: "post",
            data:{'print_id':$('#print_id').val()},
            url:'?r=small-print/ajax-small-print',
            dataType:'json',
            async:true,
            success:function(data){
           	 getObj.attr('disabled',false);
             if(data.code==404)
            	 alert(data.message);
             if(data.code==200)//打印小码单
                 console.log(data.data.data);
                 printSmallReceipt(data.data.data);
           }
    	});
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