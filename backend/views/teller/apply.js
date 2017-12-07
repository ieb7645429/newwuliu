var apply = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.confirm-collection').on('click', this._confirmCollectionClickCallBack);
        $('#searchButton').on('click', this._searchButtonClickCallBack);
        $('#downloadExcel').on('click', this._downloadExcelClickCallBack);
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
           url: '?r=teller/apply-confirm',
           data: data,
           async: true,
           dataType: 'json',
           success:function(data){
               apply._confirmSuccess(data, obj);
           }
       });
    
    },
    _confirmSuccess : function(data, obj) {
        if(data.code==200) {
            obj.parents('tr').find(".pay_time").html(data.datas.pay_time);
            obj.parents('tr').find(".pay_user").html(data.datas.pay_user);
            $('#confirm-collection_'+data.datas.id).remove();
            $('#confirm2-collection_'+data.datas.id).remove();
            $('.status_name_'+data.datas.id).html(data.datas.statusName);
        } else {
           alert(data.msg);
        }
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
    apply.init();
});
