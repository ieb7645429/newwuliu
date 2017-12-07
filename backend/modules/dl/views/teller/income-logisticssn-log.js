var incomeLogisticsSnLog = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.print').on('click', this._printClickCallBack);
    },
    
    _printClickCallBack: function() {
        $.ajax({
            type: "post",
            url:'?r=teller/income-logisticssn-log-print',
            data:{id: $(this).data('id')},
            async:true,
            success: function(data){
                if(data.code == 200) {
                    printfinancial(data.datas);
                } else {
                    alert(data.msg);
                }
            }
        });
    }
};

$(document).ready(function() {
    incomeLogisticsSnLog.init();
});