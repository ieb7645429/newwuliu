var incomeTerminusDetails = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#check_all').on('change', this._checkAll);
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('#all-confirm-collection').on('click', this._allConfirmCollectionClickCallBack);
        $('#all-confirm-collection2').on('click', this._allConfirmCollectionClickCallBack);
        $('.remark').one('click', this._remarkClickCallBack);
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
    _confirmCollectionClickCallBack : function() {
        if(!confirm('确认收款！')) {
            return ;
        }
       var obj = $(this);
       var data = {
               'order_id[]':obj.data('orderId'),
               'advance':obj.data('advance')
           };
       $.ajax({
           type: "post",
           url:'?r=dl/teller/income-terminus-confirm',
           data:data,
           async:true,
           success:incomeTerminusDetails._confirmSuccess
       });
    
    },
    _allConfirmCollectionClickCallBack : function() {
        if(!confirm('确认收款！')) {
            return ;
        }
        var order_id_array=new Array();
        $('input[name="order_id"]:checked').each(function(){  
            order_id_array.push($(this).val());//向数组中添加元素  
        });
        if(order_id_array.length==0){
            alert('请至少选择一个');
            return false;
        }
        var data = {
                'order_id[]':order_id_array,
                'advance':$(this).data('advance')
            };
        $.ajax({
            type: "post",
            url:'?r=dl/teller/income-terminus-confirm',
            data:data,
            async:true,
            success:incomeTerminusDetails._confirmSuccess
        });
        
    },_confirmSuccess : function(data) {
        var obj = $.parseJSON(data);
        if(obj.code==200){
            obj.datas.forEach(function(value){
                $('.order_check_'+value.order_id).prop('checked',false);
                $('#confirm-collection_'+value.order_id).remove();
                $('#confirm-collection2_'+value.order_id).remove();
                $('.freight_state_name_'+value.order_id).html(value.freight_state_name);
                $('.goods_price_state_name_'+value.order_id).html(value.goods_price_state_name);
                $('.order_check_'+value.order_id).attr('disabled',true);
            })
        }else {
           alert(obj.msg);
        }
    },
    _remarkClickCallBack: function() {
        var td = $(this);
        var content = td.html();
        var orderId = td.data('id');
        
        td.html('');
        var input = $('<textarea rows="3" cols="20">' +content+ '</textarea>');
        input.blur(function() {
            $.post('?r=dl/teller/order-remark', {orderId : orderId, content : input.val()}, function(data){
                if(data.code == 200) {
                    input.remove();
                    td.html(input.val());
                    td.one('click', incomeTerminusDetails._remarkClickCallBack);
                } else {
                    alert(data.msg);
                    input.remove();
                    td.html(content);
                    td.one('click', incomeTerminusDetails._remarkClickCallBack);
                }
            }, 'json');
        });
        input.appendTo(td);
        input.focus();
    }

};

$(document).ready(function() {
	incomeTerminusDetails.init();
});
