$(function(){
    $('.operation').click(function(){
    	if(!confirm("是否确定挂起？")){
    		return false;
    	} 
        var getObj = $(this);
        var data = {
            'goods_id':getObj.data('goodsId')
        };
        $.ajax({
             type: "post",
             url:'?r=terminus/goods-edit',
             data:data,
             async:true,
             success:function(data){
             var obj = $.parseJSON(data);
                if(obj.error==0){
                    var button = '<span class="lose">异常</span>';
                    getObj.after(button);
                    getObj.remove();
                 }else if(obj.error==1){
                    getObj.closest('goodsTableTr').remove();
                 }
              }
            })
        })
        
    /**
     * 落地点打印
     * 靳健
     */
    $('.js-print').click(function(){
    	if(!confirm("是否选择落地打印？")){
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
             url:'?r=terminus/goods-print',
             data:data,
             async:true,
             success:function(data){
                getObj.attr('disabled',false);
                var obj = $.parseJSON(data);
                if(obj.error==1){
                   console.log(obj.message);
                }else{
                	console.log(obj.data);
                    //printCounterfoil(obj.data);
                     printreceipt(obj.data);
                }
            }
      });
    })
    /**
     * 落地点状态修改
     * 靳健
     */
    $('.js-submit').click(function(){
    	if(!confirm("是否确定提交订单？")){
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
        		'order_arr':chk_value
            };
        $.ajax({
            type: "post",
            url:'?r=terminus/state-edit',
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
});