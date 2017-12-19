var tellerReturn = {
    order_id :'',
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.confirm-income').on('click', this._confirmIncomeClickCallBack);
        $('.confirm-pay').on('click', this._confirmPayClickCallBack);
        $('.remark_edit').on('click', this._remarkEditClickCallBack);
    },
    
    _confirmIncomeClickCallBack : function() {
        if(!confirm('确认收款！')) {
            return ;
        }
        var obj = $(this);
        var data = {
                'order_id':obj.data('orderId')
            };
        $.ajax({
            type: "post",
            url: '?r=teller/return-income-confirm',
            data: data,
            async: true,
            dataType: 'json',
            success:tellerReturn._incomeSuccess
        });
    },
    
    _incomeSuccess: function(data) {
        if(data.code==200) {
            data.datas.forEach(function(value) {
                obj = $('#confirm-income_'+value.order_id);
                obj.parents('tr').children('.goods_price_state').html(value.goods_price_state_name);
                obj.parents('tr').children('.income_time').html(value.income_time);
                
                if(value.goods_price_state_name.indexOf('已返款') == -1) {
                    var button = $('<button type="button" id="confirm-pay_'+value.order_id+'" class="btn btn-warning confirm-pay" data-order-id="'+value.order_id+'">返款</button>');
                    button.on('click', tellerReturn._confirmPayClickCallBack);
                    obj.after(button);
                }
                obj.remove();
            })
        }else {
           alert(data.msg);
        }
    },

    _confirmPayClickCallBack : function() {
       if(!confirm('确认收款！')) {
           return ;
       }
       var obj = $(this);
       var data = {
               'order_id':obj.data('orderId')
           };
       $.ajax({
           type: "post",
           url: '?r=teller/return-pay-confirm',
           data:data,
           async:true,
           dataType: 'json',
           success:tellerReturn._confirmSuccess
       });
    
    },
    _confirmSuccess : function(data) {
        if(data.code==200){
            data.datas.forEach(function(value) {
                obj = $('#confirm-pay_'+value.order_id);
                $('#confirm-pay_'+value.order_id).remove();
                obj.parents('tr').children('.goods_price_state').html(value.goods_price_state_name);
                obj.parents('tr').children('.pay_time').html(value.pay_time);
            })
        }else {
           alert(data.msg);
        }
    },

    _remarkEditClickCallBack: function() {
        $('#remark-modal-body').empty();
        tellerReturn.order_id = $(this).data('id');
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-init',
            data:{id: tellerReturn.order_id},
            async:true,
            success: tellerReturn._remarkEditSuccess
        });
    },
    
    _remarkEditSuccess : function(data) {
        if(data.code == 200) {
            var table = $('<table class="table table-bordered table-hover"></table>');
            var flag = true;
            if(data.datas.remarks != "") {
                $.each(data.datas.remarks,function(i, item) {
                    var content = '';
                    if(item.edit == true) {
                        content = '<input type="text" class="form-control" name="order_remark" id="order_remark" value="'+item.content+'">';
                        flag = false;
                    } else {
                        content = item.content;
                    }
                    var tr = $('<tr><td>'+item.user_name+'</td><td>'+item.add_time+'</td><td>'+content+'</td></tr>');
                    tr.appendTo(table);
                });
            }
            if(flag) {
                var tr = $('<tr><td>'+data.datas.user_name+'</td><td></td><td><input type="text" class="form-control" name="order_remark" id="order_remark" value=""></td></tr>');
                tr.appendTo(table);
            }
            var tr = $('<tr><td colspan="3"></td></tr>');
            var button = $('<button type="button" id="remark_save" class="btn btn-info">保存</button>');
            button.click(tellerReturn._remarkSaveClickCallBack);
            button.appendTo(tr.children('td'));
            tr.appendTo(table);
            table.appendTo($('#remark-modal-body'));
        }
    },
    
    _remarkSaveClickCallBack : function() {
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-save',
            data:{id: tellerReturn.order_id, content: $('#order_remark').val()},
            async:true,
            success: function(data) {
                if(data.code == 200) {
                    window.location.reload();
                } else {
                    alert(data.msg);
                }
            }
        });
    },

};

$(document).ready(function() {
    tellerReturn.init();
});
