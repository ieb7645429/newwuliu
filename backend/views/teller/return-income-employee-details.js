var returnIncomeEmployeeDetails = {
    order_id: '',
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#return_check_all').on('change', this._checkAll);
        $('.return_confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('#return_all-confirm-collection').on('click', this._allConfirmCollectionClickCallBack);
//        $('.return_remark').one('click', this._remarkClickCallBack);
        $('.return_remark_edit').on('click', this._remarkEditClickCallBack);
    },

    _checkAll : function() {
        if($('#return_check_all').is(':checked')){
          $('.return_order_check').each(function(){
            if(!$(this).prop('disabled')){
               $(this).prop('checked',true);
            }
          })
        }else{
          $('.return_order_check').prop('checked',false);
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
           url:'?r=teller/return-income-employee-confirm',
           data:data,
           async:true,
           dataType: 'json',
           success:returnIncomeEmployeeDetails._confirmSuccess
       });
    
    },
    _allConfirmCollectionClickCallBack : function() {
        if(!confirm('确认收款！')) {
            return ;
        }
        var order_id_array=new Array();
        $('input[name="return_order_id"]:checked').each(function(){  
            order_id_array.push($(this).val());//向数组中添加元素  
        });
        if(order_id_array.length==0){
            alert('请至少选择一个');
            return false;
        }
        var data = {
                'order_id[]':order_id_array,
            };
        $.ajax({
            type: "post",
            url:'?r=teller/return-income-employee-confirm',
            data:data,
            async:true,
            dataType: 'json',
            success:returnIncomeEmployeeDetails._confirmSuccess
        });
        
    },
    _confirmSuccess : function(data) {
        if(data.code == 200) {
            $.each(data.datas, function(i, value){
                $('#return_confirm-collection_'+value.order_id).remove();
                $('.return_freight_state_name_'+value.order_id).html(value.freight_state_name);
				$('.return_order_check' + value.order_id).attr('data-id','');
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
            $.post('?r=teller/return-order-remark', {orderId : orderId, content : input.val()}, function(data){
                if(data.code == 200) {
                    input.remove();
                    td.html(input.val());
                    td.one('click', returnIncomeEmployeeDetails._remarkClickCallBack);
                } else {
                    alert(data.msg);
                    input.remove();
                    td.html(content);
                    td.one('click', returnIncomeEmployeeDetails._remarkClickCallBack);
                }
            }, 'json');
        });
        input.appendTo(td);
        input.focus();
    },
    _remarkEditClickCallBack: function() {
        $('#remark-modal-body').empty();
        returnIncomeEmployeeDetails.order_id = $(this).data('id');
        $.ajax({
            type: "post",
            url:'?r=teller/return-order-remark-init',
            data:{id: returnIncomeEmployeeDetails.order_id},
            async:true,
            success: returnIncomeEmployeeDetails._remarkEditSuccess
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
            button.click(returnIncomeEmployeeDetails._remarkSaveClickCallBack);
            button.appendTo(tr.children('td'));
            tr.appendTo(table);
            table.appendTo($('#remark-modal-body'));
        }
    },
    
    _remarkSaveClickCallBack : function() {
        $.ajax({
            type: "post",
            url:'?r=teller/return-order-remark-save',
            data:{id: returnIncomeEmployeeDetails.order_id, content: $('#order_remark').val()},
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
    returnIncomeEmployeeDetails.init();
});
