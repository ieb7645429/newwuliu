var incomeLogisticsSn = {
    text_index : 1,
    orderIds : [],
    init : function() {
        this.orderIds = [];
        $('#logistics_sn_message').hide();
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#logistics_sn').on('blur', this._logisticsSnBlurCallBack);
        $('#logistics_sn').keydown(function(event) {
            if(event.which == 13) {
                event.stopPropagation();
                event.preventDefault();
                $('#logistics_sn').blur();
            }
        });
        $('#all-confirm-collection').on('click', this._allConfirmCollectionClickCallBack);
        $('#all-confirm-collection2').on('click', this._allConfirmCollectionClickCallBack);
    },
    
    _logisticsSnBlurCallBack : function() {
        var obj = $(this);
        if(obj.val() == '') {
            obj.next('div').show();
            obj.next('div').html('请输入票号!');
            
            incomeLogisticsSn._cleanData(obj);
            return ;
        }
        if(obj.val().length < 5) {
            obj.next('div').show();
            obj.next('div').html('请至少输入5位票号!');
            
            incomeLogisticsSn._cleanData(obj);
            obj.focus();
            return ;
        }
        $.ajax({
            type: "post",
            url:'?r=teller/income-logisticssn-details',
            data:{logistics_sn: obj.val()},
            async:true,
            dataType: 'json',
            success: function(data) {
                incomeLogisticsSn._getOrder(data, obj);
            }
       });
    },
    
    _getOrder: function(data, obj) {
        if(data.code==200) {
            if($.inArray(data.datas.order_id, incomeLogisticsSn.orderIds) == -1) {
                
                obj.val(data.datas.logistics_sn);
                obj.nextAll('.orderId').val(data.datas.order_id);
                obj.parents('tr').children('.amount').html(data.datas.all_amount);
                
                var newTr = true;
                $('.logistics_sn').each(function(i, item) {
                    if($(item).val() == ''){
                        newTr = false;
                        $(item).focus();
                    }
                });
                if(newTr) {
                    var tr = $('<tr></tr>');
                    var td = $('<td></td>');
                    var text = $('<input type="text" class="form-control logistics_sn" name="" value="" data-index="' + (++incomeLogisticsSn.text_index) + '">');
                    text.on('blur', incomeLogisticsSn._logisticsSnBlurCallBack);
                    text.keydown(function(event) {
                        if(event.which == 13) {
                            event.stopPropagation();
                            event.preventDefault();
                            text.blur();
                        }
                    });
                    text.appendTo(td);
                    $('<div class="alert alert-danger" style="display:none;" role="alert"></div>').appendTo(td);
                    $('<input type="hidden" class="orderId" name="order_id[]" value="">').appendTo(td);
                    td.appendTo(tr);
                    $('<td class="amount"></td>').appendTo(tr);
                    $('.all_amount_body').before(tr);
                    text.focus();
                }
                obj.next('div').hide();
                obj.next('div').html('');
                incomeLogisticsSn._setAllAmount();
                incomeLogisticsSn._setOrderIds();
            } else {
                var focusFlag = true;
                $('.logistics_sn').each(function(i, item) {
                    if($(item).val().indexOf(obj.val()) > -1 && $(item).data('index') != obj.data('index')) {
                        obj.next('div').show();
                        obj.next('div').html('票号已经存在！');
                        obj.focus();
                        focusFlag = false;
                        
                        incomeLogisticsSn._cleanData(obj);
                    }
                });
                if(focusFlag) {
                    $('.logistics_sn').each(function(i, item) {
                        if($(item).val() == '') {
                            $(item).focus();
                        }
                    });
                }
            }
         } else {
            obj.next('div').show();
            obj.next('div').html(data.msg);
            obj.val('');
            obj.focus();
            incomeLogisticsSn._cleanData(obj);
         }
    },
    
    _setAllAmount: function() {
        var all_amount = 0;
        $('.amount').each(function(i, item) {
            if($(item).text()) {
                all_amount += parseFloat($(item).text());
            }
        });
        $('#all_amount').html(all_amount);
    },
    
    _setOrderIds: function() {
        incomeLogisticsSn.orderIds = [];
        $('.orderId').each(function(i, item) {
            if($(item).val()){
                incomeLogisticsSn.orderIds.push($(item).val());
            }
        });
    },
    
    _cleanData: function(obj) {
        obj.nextAll('.orderId').val('');
        obj.parents('tr').children('.amount').html('');
        incomeLogisticsSn._setAllAmount();
        incomeLogisticsSn._setOrderIds();
    },
    
    _allConfirmCollectionClickCallBack : function() {
        if(!confirm('确认收款！')) {
            return ;
        }
        if(incomeLogisticsSn.orderIds.length == 0) {
            alert("请至少输入一个票号！");
            return ;
        }
        if($('#receiving').val() == '') {
            alert("请输入收款对象！");
            $('#receiving').focus();
            return ;
        }
        var data = {
            'order_id[]': incomeLogisticsSn.orderIds,
            'advance': $(this).data('advance'),
            'receiving': $('#receiving').val()
        };
        $.ajax({
            type: "post",
            url:'?r=teller/income-logisticssn-confirm',
            data:data,
            async:true,
            success:incomeLogisticsSn._confirmSuccess
        });
    },
    
    _confirmSuccess: function(data) {
        console.log(data);
        if (data.code == 200) {
            window.location.href='?r=teller/income-logisticssn-log';
        } else {
            alert(data.msg);
        }
    }
};

$(document).ready(function() {
    incomeLogisticsSn.init();
});