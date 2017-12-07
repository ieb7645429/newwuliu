var employeeIndex = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#searchButton').on('click', this._searchButtonClickCallBack);
        $('#downloadExcel').on('click', this._downloadExcelClickCallBack);
        $('#checkbox-input').on('click', this._callBackEmployeePrint)
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
    },
    _callBackEmployeePrint: function() {
    	var obj = $(this);
    	obj.attr('disabled','disabled');
    	$.ajax({
            type: "post",
            url:'?r=hlj/employee/ajax-print-change',
            dataType:'json',
            async:true,
            success:function(data){
                obj.attr('disabled',false);
                if(data.code == 400){
                    alert(data.message);
                }
                if(data.code == 200){
                    alert(data.message);
                    if(data.status==1){
                        obj.attr('checked',true);
                    }
                    if(data.status==0){
                    	obj.attr('checked',false)
                    }
                }
           }
        });
    }
    
};

$(document).ready(function() {
    employeeIndex.init();
});
