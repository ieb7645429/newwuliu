var payTerminusDetails = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#check_all').on('change', this._checkAll);
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('#all-confirm-collection').on('click', this._allConfirmCollectionClickCallBack);
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
        if(!confirm('确认付款！')) {
            return ;
        }
       var obj = $(this);
       var data = {
               'order_id[]':obj.data('orderId'),
           };
       $.ajax({
           type: "post",
           url:'?r=dl/teller/pay-terminus-confirm',
           data:data,
           async:true,
           dataType:'json',
           success:payTerminusDetails._confirmSuccess
       });
    },

    _allConfirmCollectionClickCallBack : function() {
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
            url: '?r=dl/teller/pay-terminus-confirm',
            data: data,
            async: true,
            dataType: 'json',
            success: payTerminusDetails._confirmSuccess
        });
    },
    
    _confirmSuccess : function(data) {
        if(data.code==200){
           $.each(data.datas, function(i, item){
               $("#confirm-collection_" + item.order_id).remove();
               $('.order_check_' + item.order_id).prop('checked',false);
               $('.order_check_' + item.order_id).attr('disabled',true);
               $('.freight_state_name_' + item.order_id).html(item.freight_state_name);
           });
        }else {
           alert(data.msg);
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
                    td.one('click', payTerminusDetails._remarkClickCallBack);
                } else {
                    alert(data.msg);
                    input.remove();
                    td.html(content);
                    td.one('click', payTerminusDetails._remarkClickCallBack);
                }
            }, 'json');
        });
        input.appendTo(td);
        input.focus();
    }

};

$(document).ready(function() {
    payTerminusDetails.init();
});
