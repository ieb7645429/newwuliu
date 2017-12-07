var advance = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
    },

    _confirmCollectionClickCallBack : function() {
       if(!confirm('确认已经收款！')) {
           return ;
       }
       var obj = $(this);
       var data = {
           'id':obj.data('id'),
       };
       $.ajax({
           type: "post",
           url: '?r=dl/teller/advance-confirm',
           data: data,
           async: true,
           dataType: 'json',
           success:advance._confirmSuccess
       });
    
    },
    _confirmSuccess : function(data) {
        if(data.code==200) {
            $('#confirm-collection_'+data.datas.id).remove();
            $('.state_name_'+data.datas.id).html(data.datas.stateName);
            $('.income_time_'+data.datas.id).html(data.datas.incomeTime);
        }else {
           alert(data.msg);
        }
    }
};

$(document).ready(function() {
    advance.init();
});
