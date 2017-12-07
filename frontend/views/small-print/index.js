$(function(){
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