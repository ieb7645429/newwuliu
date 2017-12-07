var terminusPay = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('.remark').one('click', this._remarkClickCallBack);
        $('.return_remark').one('click', this._returnRemarkClickCallBack);
    },
    _remarkClickCallBack: function() {
        var td = $(this);
        var content = td.html();
        var orderId = td.data('id');
        
        td.html('');
        var input = $('<textarea rows="3" cols="20">' +content+ '</textarea>');
        input.blur(function() {
            $.post('?r=terminus/order-remark', {orderId : orderId, content : input.val()}, function(data){
                if(data.code == 200) {
                    input.remove();
                    td.html(input.val());
                    td.one('click', terminusPay._remarkClickCallBack);
                } else {
                    alert(data.msg);
                    input.remove();
                    td.html(content);
                    td.one('click', terminusPay._remarkClickCallBack);
                }
            }, 'json');
        });
        input.appendTo(td);
        input.focus();
    },
    _returnRemarkClickCallBack: function() {
        var td = $(this);
        var content = td.html();
        var orderId = td.data('id');
        
        td.html('');
        var input = $('<textarea rows="3" cols="20">' +content+ '</textarea>');
        input.blur(function() {
            $.post('?r=terminus/return-order-remark', {orderId : orderId, content : input.val()}, function(data){
                if(data.code == 200) {
                    input.remove();
                    td.html(input.val());
                    td.one('click', terminusPay._returnRemarkClickCallBack);
                } else {
                    alert(data.msg);
                    input.remove();
                    td.html(content);
                    td.one('click', terminusPay._returnRemarkClickCallBack);
                }
            }, 'json');
        });
        input.appendTo(td);
        input.focus();
    }
}

$(document).ready(function() {
    terminusPay.init();
});