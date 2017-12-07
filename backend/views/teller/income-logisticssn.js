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
        $('#rel_amount').on('blur', this._setRelAllAmount);
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
                obj.parents('tr').find('.rel_amount').val(data.datas.all_amount);
                
                var newTr = true;
                $('.logistics_sn').each(function(i, item) {
                    if($(item).val() == ''){
                        newTr = false;
                        $(item).focus();
                    }
                });
                if(newTr) {
                    var tr = $('<tr class="newTr"></tr>');
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
                    
                    var input = $('<input type="text" class="form-control rel_amount" name="" value="">');
                    input.on('blur', function(){
                        incomeLogisticsSn._setRelAllAmount();
                    });
                    var td2 = $('<td></td>');
                    input.appendTo(td2);
                    td2.appendTo(tr);
                    
                    $('.all_amount_body').before(tr);
                    text.focus();
                }
                obj.next('div').hide();
                obj.next('div').html('');
                incomeLogisticsSn._setAllAmount();
                incomeLogisticsSn._setRelAllAmount();
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
    
    _setRelAllAmount: function() {
        var all_amount = 0;
        var re = /^\d+(\.\d+)?$/;
        $('.rel_amount').each(function(i, item) {
            if($(item).val()) {
                if (re.test($(item).val())) {
                    all_amount += parseFloat($(item).val());
                }
            }
        });
        $('#rel_all_amount').html(all_amount);
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
        obj.parents('tr').find('.rel_amount').val('');
        incomeLogisticsSn._setAllAmount();
        incomeLogisticsSn._setRelAllAmount();
        incomeLogisticsSn._setOrderIds();
    },
    
    _allConfirmCollectionClickCallBack : function() {
        $('#all-confirm-collection').prop("disabled", true);
        $('#all-confirm-collection2').prop("disabled", true);

        if(!confirm('确认收款！')) {
            $('#all-confirm-collection').prop("disabled", false);
            $('#all-confirm-collection2').prop("disabled", false);
            return ;
        }
        if(incomeLogisticsSn.orderIds.length == 0) {
            alert("请至少输入一个票号！");
            $('#all-confirm-collection').prop("disabled", false);
            $('#all-confirm-collection2').prop("disabled", false);
            return ;
        }
        if($('#receiving').val() == '') {
            alert("请输入收款对象！");
            $('#all-confirm-collection').prop("disabled", false);
            $('#all-confirm-collection2').prop("disabled", false);
            $('#receiving').focus();
            return ;
        }
        var ids = [];
        var amounts = [];
        var rel_amounts = [];
        $('.income_logistics_sn').children('tr').each(function(i, item) {
            if($(item).find('.orderId').val()) {
                ids.push($(item).find('.orderId').val());
                amounts.push($(item).find('.amount').text());
                if($(item).find('.rel_amount').val()) {
                    rel_amounts.push($(item).find('.rel_amount').val());
                } else {
                    rel_amounts.push(0);
                }
            }
        });
        var data = {
            'order_id[]': ids,
            'amount[]': amounts,
            'rel_amount[]': rel_amounts,
            'advance': $(this).data('advance'),
            'receiving': $('#receiving').val()
        };
        $.ajax({
            type: "post",
            url:'?r=teller/income-logisticssn-confirm',
            data:data,
            async:true,
            success:incomeLogisticsSn._confirmSuccess,
            error: function(){
                alert('收款失败！');
                $('#all-confirm-collection').prop("disabled", false);
                $('#all-confirm-collection2').prop("disabled", false);
            }
        });
    },
    
    _confirmSuccess: function(data) {
        if (data.code == 200) {
            $('.newTr').remove();
            var obj = $('#logistics_sn');
            obj.val('');
            $('#receiving').val('');
            incomeLogisticsSn._cleanData(obj);
            
            $('#all-confirm-collection').prop("disabled", false);
            $('#all-confirm-collection2').prop("disabled", false);
            
            printfinancial_T(data.datas);
        } else {
            alert(data.msg);
            $('#all-confirm-collection').prop("disabled", false);
            $('#all-confirm-collection2').prop("disabled", false);
        }
    }
};

$(document).ready(function() {
    incomeLogisticsSn.init();
});