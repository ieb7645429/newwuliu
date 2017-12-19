var createUserSearch = {
    init: function() {
        this._setEvent();
    },
    _setEvent: function() {
        $('#searchButton').on('click', this.searchButtonClickCallBack);
    },
    searchButtonClickCallBack: function() {
		$('#searchButton').attr('disabled',true);
		$('#searchButton').html('查询中');
        $.ajax({
            type: "get",
            async: true,
            data:{contacts_phone: $('#phone').val(), store_name: $('#store_name').val(), small_num: $('#small_num').val(),area:'sy'},
            url:'https://www.youjian8.com/mobile/index.php?act=seller_reg&op=get_small_num',
            dataType: "jsonp",
            jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
            jsonpCallback:"flightHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
            success: function(json) {
              console.log(json);
              if(json.datas) {
                  var html = '';
                  $.each(json.datas, function(i, n) {
                      html += '<div style="margin-bottom:10px;">'
                      html += '<span style="margin-right:5px;">';
                      html += '会员小号：' + n.small_num;
                      html += '</span><span style="margin-right:5px;">';
                      html += '店铺名：' + n.store_name;
                      html += '</span><span>';
                      html += '电话：' + n.member_name;
                      html += '</span>';
                      html += '</div>';
                  });
                  $('#result').html(html);				 
              }else{
                  $('#result').html('未注册会员');
              }
			   $('#searchButton').attr('disabled',false);
		       $('#searchButton').html('查询');
            },
            error: function() {
               alert('查询失败！');
            }
        });
    }
};

$(document).ready(function() {
    createUserSearch.init();
});