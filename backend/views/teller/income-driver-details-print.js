$(function(){
        $('.js-loading-print').click(function(){
			//判断是否有选中的要打印项selection
			var value = '';
			var flag  = false;
			$('.order_check').each(function(index, element) {
                if($(this).is(':checked')){
				 value+= $(this).val()+',';
				}
            });
			value = value.substring(0,(value.length-1));
			if(value.length==0){
			 alert('请选择要打印的信息');
			 return;	
			}
            var getObj = $(this);
           // getObj.attr('disabled',true);
			var data = Array();
			$('.order_check').each(function(index, element) {
			var	val = Array();
			   //判断是否选中
			   if($('.order_check').eq(index).is(":checked")){
			   //判断是否满足打印条件
			     if($('.order_check').eq(index).attr('data-id')==''){
					// console.log(index);
						val['logistics_sn'] = $('.sn').eq(index).attr('data-id');
						val['amount']      = $('.amount').eq(index).attr('data-id');
						data.push(val);
						flag = true;
				 }
				 else{
					  if($('#check_all').is(":checked")==false){
						 alert('当前为未收款状态,不能打印,请先收款在打印');
						 return;// flag = false ;    
					  }				 
			     }
			   }		  
            });
			//console.log(data);
			if(flag == true){
			  printfinancial(data);
			}
			//console.log(data['amount'][0]);
			//return;
          /*  var data = {
                'loading':1,
                'order_sn':value,
            };*/
			//$('.js-loading-print').attr('disabled',false);
			// printfinancial(data);
          /*  $.ajax({
                 type: "post",
                 url:'?r=teller/print-confirm',
                 data:data,
                 async:true,
                 dataType:'json',
                 success:function(data){
					$('.js-loading-print').attr('disabled',false);
                    if(data.error==1){
                       console.log(data.message);
                       location.replace(location.href);
                    }else{
                        printfinancial(data.data);
                        console.log(obj.data);
                    }
                }
          });*/
        });
      
});