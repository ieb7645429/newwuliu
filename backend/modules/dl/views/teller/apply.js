var apply = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
    },

    _confirmCollectionClickCallBack : function() {
       if(!confirm('确认已经付款！')) {
           return ;
       }
       var obj = $(this);
       var data = {
           'id':obj.data('id'),
       };
       $.ajax({
           type: "post",
           url: '?r=dl/teller/apply-confirm',
           data: data,
           async: true,
           dataType: 'json',
           success:apply._confirmSuccess
       });
    
    },
    _confirmSuccess : function(data) {
        if(data.code==200) {
            $('#confirm-collection_'+data.datas.id).remove();
            $('#confirm2-collection_'+data.datas.id).remove();
            $('.status_name_'+data.datas.id).html(data.datas.statusName);
        }else {
           alert(data.msg);
        }
    }
};

$(document).ready(function() {
    apply.init();
});
