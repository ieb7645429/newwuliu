var tellerSearch = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('.remark').one('click', this._remarkClickCallBack);
    },

    _confirmCollectionClickCallBack : function() {
       if(!confirm('确认收款！')) {
           return ;
       }
       var obj = $(this);
       var data = {
               'order_id[]':obj.data('orderId'),
               'advance':obj.data('advance'),
           };
       $.ajax({
           type: "post",
           url:obj.data('url'),
           data:data,
           async:true,
           dataType: 'json',
           success:tellerSearch._confirmSuccess
       });
    
    },
    _confirmSuccess : function(data) {
        if(data.code==200){
            data.datas.forEach(function(value) {
                if (!value.goods_price_state_name) {
                    $('#confirm-freight_'+value.order_id).remove();
                } else {
                    $('#confirm-freight_goods_'+value.order_id).remove();
                    $('#confirm-freight_goods2_'+value.order_id).remove();
                }
                if(value.freight_state_name) {
                    $('.freight_state_name_'+value.order_id).html(value.freight_state_name);
                }
                if(value.goods_price_state_name) {
                    $('.goods_price_state_name_'+value.order_id).html(value.goods_price_state_name);
                }
            })
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
                    td.one('click', tellerSearch._remarkClickCallBack);
                } else {
                    alert(data.msg);
                    input.remove();
                    td.html(content);
                    td.one('click', tellerSearch._remarkClickCallBack);
                }
            }, 'json');
        });
        input.appendTo(td);
        input.focus();
    }

};

$(document).ready(function() {
    tellerSearch.init();
});
