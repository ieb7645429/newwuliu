$(function(){
	/**
     * order状态修改
     * 靳健
     */
     $('.operation').click(function(){
    	 if(!confirm("是否确定收款完成？")){
     		return false;
     	} 
         var getObj = $(this);
         var orderId = getObj.data('orderId');
         var data = {
             'order_id':getObj.data('orderId')
         };
         $.ajax({
              type: "post",
              url:'?r=dl/terminus/last-edit',
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
	
	
	
})