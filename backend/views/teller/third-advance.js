var thirdAdvance = {
    order_id :'',
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.confirm-advance').on('click', this._confirmAdvanceClickCallBack);
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('.remark_edit').on('click', this._remarkEditClickCallBack);
    },
    
    _confirmAdvanceClickCallBack : function() {
        if(!confirm('确认垫付！')) {
            return ;
        }
        var obj = $(this);
        var data = {
                'order_id':obj.data('orderId')
            };
        $.ajax({
            type: "post",
            url: '?r=teller/third-advance-confirm',
            data: data,
            async: true,
            dataType: 'json',
            success:thirdAdvance._advanceSuccess
        });
    },
    
    _advanceSuccess: function(data) {
        if(data.code==200) {
            data.datas.forEach(function(value) {
                obj = $('#confirm-advance_'+value.order_id);
                obj.parents('tr').children('.advance_state').html(value.advance_state);
                obj.parents('tr').children('.advance_time').html(value.advance_time);
                obj.parents('tr').children('.advance_user').html(value.advance_user);
                
                if(value.order_state >= 50) {
                    var button = $('<button type="button" id="confirm-goods_'+value.order_id+'" class="btn btn-danger confirm-collection" data-url="'+value.url+'" data-order-id="'+value.order_id+'">收款</button>');
                    button.on('click', thirdAdvance._confirmCollectionClickCallBack);
                    obj.after(button);
                }
                obj.remove();
            })
        }else {
           alert(data.msg);
        }
    },

    _confirmCollectionClickCallBack : function() {
       if(!confirm('确认收款！')) {
           return ;
       }
       var obj = $(this);
       var data = {
               'order_id[]':obj.data('orderId')
           };
       $.ajax({
           type: "post",
           url:obj.data('url'),
           data:data,
           async:true,
           dataType: 'json',
           success:thirdAdvance._confirmSuccess
       });
    
    },
    _confirmSuccess : function(data) {
        if(data.code==200){
            data.datas.forEach(function(value) {
                $('#confirm-goods_'+value.order_id).remove();
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
                    td.one('click', thirdAdvance._remarkClickCallBack);
                } else {
                    alert(data.msg);
                    input.remove();
                    td.html(content);
                    td.one('click', thirdAdvance._remarkClickCallBack);
                }
            }, 'json');
        });
        input.appendTo(td);
        input.focus();
    },
    _remarkEditClickCallBack: function() {
        $('#remark-modal-body').empty();
        thirdAdvance.order_id = $(this).data('id');
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-init',
            data:{id: thirdAdvance.order_id},
            async:true,
            success: thirdAdvance._remarkEditSuccess
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
            button.click(thirdAdvance._remarkSaveClickCallBack);
            button.appendTo(tr.children('td'));
            tr.appendTo(table);
            table.appendTo($('#remark-modal-body'));
        }
    },
    
    _remarkSaveClickCallBack : function() {
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-save',
            data:{id: thirdAdvance.order_id, content: $('#order_remark').val()},
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
    thirdAdvance.init();
});
