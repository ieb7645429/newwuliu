var payDealerDetails = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#check_all').on('change', this._checkAll);
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('#all-confirm-collection').on('click', this._allConfirmCollectionClickCallBack);
    },

    _checkAll : function() {
        if($('#check_all').is(':checked')){
          $('.order_check').each(function(){
            if(!$(this).prop('disabled')){
               $(this).prop('checked',true);
            }
          })
        }else{
          $('.order_check').prop('checked',false);
        }
    },
    _confirmCollectionClickCallBack : function(){
        if(!confirm('确认付款！')) {
            return ;
        }
        var obj = $(this);
        var data = {
                'order_id[]':obj.data('orderId'),
            };
        $.ajax({
            type: "post",
            url:'?r=teller/pay-dealer-confirm',
            data:data,
            async:true,
            dataType:'json',
            success:payDealerDetails._confirmSuccess
        });
    },
    _allConfirmCollectionClickCallBack : function(){
        if(!confirm('确认付款！')) {
            return ;
        }
        var order_id_array=new Array();
        $('input[name="order_id"]:checked').each(function(){  
            order_id_array.push($(this).val());//向数组中添加元素  
        });
        if(order_id_array.length==0){
            alert('请选择确认付款订单');
            return ;
        }
        var data = {
                'order_id[]':order_id_array,
            };
        $.ajax({
            type: "post",
            url: '?r=teller/pay-dealer-confirm',
            data: data,
            async: true,
            dataType: 'json',
            success: payDealerDetails._confirmSuccess
        });
    },
    
    _confirmSuccess : function(data) {
        if(data.code==200){
           $.each(data.datas, function(i, item){
               $("#confirm-collection_" + item.order_id).remove();
               $('.order_check_' + item.order_id).prop('checked',false);
               $('.order_check_' + item.order_id).attr('disabled',true);
               $('.goods_price_state_name_' + item.order_id).html(item.goods_price_state_name);
           });
        }else {
           alert(data.msg);
        }
    }

};

$(document).ready(function() {
	payDealerDetails.init();
});
