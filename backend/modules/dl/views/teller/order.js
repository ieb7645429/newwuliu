var order = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#searchButton').on('click', this._searchButtonClickCallBack);
        $('#downloadExcel').on('click', this._downloadExcelClickCallBack);
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
    order.init();
});
