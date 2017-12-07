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
            url:'?r=dl/small-print/ajax-small-print',
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
})