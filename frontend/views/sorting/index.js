
$(function(){
    $('.operation').click(function(){
    	if(!confirm("是否选择处理订单？")){
     		return false;
     	}
        var getObj = $(this);
        var orderId = getObj.data('orderId');
        var data = {
            'goods_id':getObj.data('goodsId')
        };
        $.ajax({
             type: "post",
             url:'?r=sorting/return-edit',
             data:data,
             async:true,
             success:function(data){
             var obj = $.parseJSON(data);
                if(obj.error==0){
                    var button = '<span class="finish">已处理</span>';
                    getObj.after(button);
                    getObj.remove();
                    $('.checkbox'+orderId).attr("checked", true);
                   //printreceipt(obj.data);
                 }else if(obj.error==1){
                    alert(obj.message);
                 }
              }
            })
        })
    $('.js-print').click(function(){
    	if(!confirm("是否选择退货打印？")){
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
                'order_arr':chk_value
                
            };
        $.ajax({
             type: "post",
             url:'?r=sorting/goods-print',
             data:data,
             async:true,
             success:function(data){
                getObj.attr('disabled',false);
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
                    console.log(obj.data);
                    printCounterfoil(obj.data,1);
                    //location.replace(location.href);
                   //console.log(obj.data);
                }
            }
      });
    })
    
    $('.js-goods-submit').click(function(){
    	if(!confirm("是否选择批量处理？")){
     		return false;
     	}
        var getObj = $(this);
        var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择打印订单');
            return false;
        }
        getObj.attr('disabled','disabled');
        $.ajax({
             type: "post",
             url:'?r=sorting/goods-batch-edit',
             dataType:'json',
             async:true,
             success:function(data){
                getObj.attr('disabled',false);
                alert(data.message);
                for(var i=0;i<data.data.length;i++){
                	$('.order_'+data.data[i]).removeClass('operation').addClass('finish');
                }
            }
      });
    })
    
    $('.js-submit').click(function(){
    	if(!confirm("是否确定提交处理？")){
     		return false;
     	}
        var getObj = $(this);
        var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择打印订单');
            return false;
        }
        getObj.attr('disabled','disabled');
        var data = {
        		'order_state':10,
        };
        $.ajax({
             type: "post",
             url:'?r=sorting/order-edit',
             data:data,
             async:true,
             success:function(data){
            	 getObj.attr('disabled',false);
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   alert(obj.message);
                }else{
                    alert(obj.message);
                    location.replace(location.href);
                   //console.log(obj.data);
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
})/**
 * 
 */