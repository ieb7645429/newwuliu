var returnIncomeEmployeeDetails = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#return_check_all').on('change', this._checkAll);
        $('.return_confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('#return_all-confirm-collection').on('click', this._allConfirmCollectionClickCallBack);
        $('.return_remark').one('click', this._remarkClickCallBack);
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
           url:'?r=dl/teller/return-income-employee-confirm',
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
            url:'?r=dl/teller/return-income-employee-confirm',
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
            $.post('?r=dl/teller/return-order-remark', {orderId : orderId, content : input.val()}, function(data){
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
    }

};

$(document).ready(function() {
    returnIncomeEmployeeDetails.init();
});
