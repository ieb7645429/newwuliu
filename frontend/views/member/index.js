$(function(){
//        $('.js-loading-print').click(function(){
//        	if(!confirm("是否确定打印？")){
//        		return false;
//        	}
//			//判断是否有选中的要打印项selection
//			var value = '';
//			$('.checkbox').each(function(index, element) {
//                if($(this).is(':checked')){
//				 value+= $(this).val()+',';
//				}
//            });
//			value = value.substring(0,(value.length-1));
//			if(value.length==0){
//			 alert('请选择要打印的信息');
//			 return;	
//			}
//            var getObj = $(this);
//            getObj.attr('disabled',true);
//            var data = {
//                'loading':1,
//                'order_sn':value,
//            };
//            $.ajax({
//                 type: "post",
//                 url:'?r=member/print-selectedlist',
//                 data:data,
//                 async:true,
//                 dataType:'json',
//                 success:function(data){
//					$('.js-loading-print').attr('disabled',false);
//                    if(data.error==1){
//                       console.log(data.message);
//                       location.replace(location.href);
//                    }else{
//                        printCounterfoil(data.data);
//                        console.log(data.data);
//                    }
//                }
//          });
//        });
	$('.js-loading-print').click(function(){
    	if(!confirm("是否确定打印？")){
    		return false;
    	}
		//判断是否有选中的要打印项selection
		var value = '';
		$('.checkbox').each(function(index, element) {
            if($(this).is(':checked')){
			 value+= $(this).val()+',';
			}
        });
		value = value.substring(0,(value.length-1));
		if(value.length==0){
		 alert('请选择要打印的信息');
		 return;	
		}
		console.log(value);
		window.open("?r=member/print&order_id="+value);
		//$('#print').submit();
        
    });
      
});