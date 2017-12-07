var incomeLogisticsSnLog = {
    order_id :'',
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#printButton').on('click', this._printClickCallBack);
        $('#searchButton').on('click', this._searchButtonClickCallBack);
        $('#downloadExcel').on('click', this._downloadExcelClickCallBack);
        $('.remark_edit').on('click', this._remarkEditClickCallBack);
    },
    
    _printClickCallBack: function() {
        if($('#remark_number').val() == "") {
            alert('打印请输入编号！');
            $('#remark_number').focus();
            return ;
        }
        $.ajax({
            type: "post",
            url:'?r=teller/income-logisticssn-log-print',
            data:{id: $('#remark_number').val()},
            async:true,
            success: function(data) {
                if(data.code == 200) {
                    printfinancial_T(data.datas);
                } else {
                    alert(data.msg);
                }
            }
        });
    },
    
    _remarkEditClickCallBack: function() {
        $('#remark-modal-body').empty();
        incomeLogisticsSnLog.order_id = $(this).data('id');
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-init',
            data:{id: incomeLogisticsSnLog.order_id},
            async:true,
            success: incomeLogisticsSnLog._remarkEditSuccess
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
            button.click(incomeLogisticsSnLog._remarkSaveClickCallBack);
            button.appendTo(tr.children('td'));
            tr.appendTo(table);
            table.appendTo($('#remark-modal-body'));
        }
    },
    
    _remarkSaveClickCallBack : function() {
        $.ajax({
            type: "post",
            url:'?r=teller/order-remark-save',
            data:{id: incomeLogisticsSnLog.order_id, content: $('#order_remark').val()},
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

    _searchButtonClickCallBack: function() {
        $("#download_type").val('0');
        $('#w0').attr('target', "_self");
        $('#w0').submit();
    },

    _downloadExcelClickCallBack: function() {
        $("#download_type").val('1');
        $('#w0').attr('target', "_blank");
        $('#w0').submit();
    }
};

$(document).ready(function() {
    incomeLogisticsSnLog.init();
});