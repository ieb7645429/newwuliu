var tellerSearch = {
    order_id :'',
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
//        $('.remark').one('click', this._remarkClickCallBack);
        $('.remark_edit').on('click', this._remarkEditClickCallBack);
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
            $.post('?r=teller/order-remark', {orderId : orderId, content : input.val()}, function(data){
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
    },
    _remarkEditClickCallBack: function() {
        $('#remark-modal-body').empty();
        tellerSearch.order_id = $(this).data('id');
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-init',
            data:{id: tellerSearch.order_id},
            async:true,
            success: tellerSearch._remarkEditSuccess
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
            button.click(tellerSearch._remarkSaveClickCallBack);
            button.appendTo(tr.children('td'));
            tr.appendTo(table);
            table.appendTo($('#remark-modal-body'));
        }
    },
    
    _remarkSaveClickCallBack : function() {
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-save',
            data:{id: tellerSearch.order_id, content: $('#order_remark').val()},
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
    tellerSearch.init();
});
