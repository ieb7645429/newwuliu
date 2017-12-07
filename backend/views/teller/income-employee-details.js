var incomeEmployeeDetails = {
    order_id: '',
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#check_all').on('change', this._checkAll);
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('#all-confirm-collection').on('click', this._allConfirmCollectionClickCallBack);
//        $('.remark').one('click', this._remarkClickCallBack);
        $('.remark_edit').on('click', this._remarkEditClickCallBack);
    },

    _checkAll : function() {
        if($('#check_all').is(':checked')){
          $('.order_check').each(function(){
            if(!$(this).prop('disabled')){
               $(this).prop('checked',true);
            }
          })
        }else{
          $('input[type=checkbox]').prop('checked',false);
        }
    },
    _confirmCollectionClickCallBack : function() {
        if(!confirm('确认收款！')) {
            return ;
        }
        var obj = $(this);
        var data = {
               'order_id[]':obj.data('orderId'),
           };
       $.ajax({
           type: "post",
           url:'?r=teller/income-employee-confirm',
           data:data,
           async:true,
           dataType: 'json',
               success:incomeEmployeeDetails._confirmSuccess
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
        if(order_id_array.length==0 ){
            alert('请选择确认收款订单');
            return;
        }
        var data = {
                'order_id[]':order_id_array,
            };
        $.ajax({
            type: "post",
            url:'?r=teller/income-employee-confirm',
            data:data,
            async:true,
            dataType: 'json',
            success:incomeEmployeeDetails._confirmSuccess
        });
    },
    
    _confirmSuccess : function(data) {
        if(data.code==200){
           $.each(data.datas, function(i, item){
               $("#confirm-collection_" + item.order_id).remove();
               $('.freight_state_name_' + item.order_id).html(item.freight_state_name);
			   $('.order_check_' + item.order_id).attr('data-id','');
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
            $.post('?r=teller/order-remark', {orderId : orderId, content : input.val()}, function(data){
                if(data.code == 200) {
                    input.remove();
                    td.html(input.val());
                    td.one('click', incomeEmployeeDetails._remarkClickCallBack);
                } else {
                    alert(data.msg);
                    input.remove();
                    td.html(content);
                    td.one('click', incomeEmployeeDetails._remarkClickCallBack);
                }
            }, 'json');
        });
        input.appendTo(td);
        input.focus();
    },
    
    _remarkEditClickCallBack: function() {
        $('#remark-modal-body').empty();
        incomeEmployeeDetails.order_id = $(this).data('id');
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-init',
            data:{id: incomeEmployeeDetails.order_id},
            async:true,
            success: incomeEmployeeDetails._remarkEditSuccess
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
            button.click(incomeEmployeeDetails._remarkSaveClickCallBack);
            button.appendTo(tr.children('td'));
            tr.appendTo(table);
            table.appendTo($('#remark-modal-body'));
        }
    },
    
    _remarkSaveClickCallBack : function() {
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-save',
            data:{id: incomeEmployeeDetails.order_id, content: $('#order_remark').val()},
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
    incomeEmployeeDetails.init();
});
