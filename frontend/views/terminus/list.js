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
    	var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择打印订单');
            return false;
        }
        getObj.attr('disabled','disabled');
        $.ajax({
             type: "post",
             url:'?r=terminus/goods-print',
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
    	var count = $('#count_js').val();
        if(count==0||count=='0'){
            alert('请选择打印订单');
            return false;
        }
        getObj.attr('disabled','disabled');
        $.ajax({
            type: "post",
            url:'?r=terminus/state-edit',
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
   
});