var createUserCreate = {
    init: function() {
        this._setEvent();
    },
    _setEvent: function() {
        $('#create-user-create').on('click', this.createButtonClickCallBack);
    },
    createButtonClickCallBack: function(){
        var data = {};
        data.contacts_phone = $('#createuserform-member_phone').val();
        data.company_name = $('#createuserform-user_truename').val();
        data.province_id = 6;
        data.city_id = 108;
        data.area_id = $('#createuserform-member_areaid').val();
        data.area = 'dl';
        data.company_address_detail = $('#createuserform-member_areainfo').val();
        if($('#createuserform-member_areaid').val() == '') {
            alert('区不能为空！');
            return ;
        }
        if(data.contacts_phone == '') {
            alert('电话不能为空！');
            return ;
        }
        if(!(/^1[3|4|5|7|8][0-9]{9}$/.test(data.contacts_phone))){
            alert('请输入正确手机号！');
            return ;
        }
        if(data.company_name == '') {
            alert('店铺名称不能为空！');
            return ;
        }
        
        $.ajax({
            type: "POST",
            async: true,
            data: data,
            url: 'https://www.youjian8.com/mobile/index.php?act=seller_reg&op=reg_new',
            dataType: "jsonp",
            jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
            jsonpCallback:"flightHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
            success: function(json) {
                if(json.code == 200) {
                    $('#createuserform-username').val($('#createuserform-member_phone').val());
                    $('#createuserform-small_num').val(json.small_num);
                    $('#form-signup').submit();
                } else {
                    alert(json.msg);
                }
            },
            error: function() {
               alert('注册失败！');
            }
        });
    }
};

$(document).ready(function() {
    createUserCreate.init();
});