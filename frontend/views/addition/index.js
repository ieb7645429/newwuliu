$(document).ready(function () {

    var setType = $('#subType').val();
    if (setType === 'modification') {
        $("#user-username").attr('disabled', 'disabled');
        $("#user-email").attr('disabled', 'disabled');
        $("#user-member_phone").attr('disabled', 'disabled');
        $("#user-user_truename").attr('disabled', 'disabled');
        $("#authitem-name").attr('disabled', 'disabled');
        $("#user-member_provinceid").attr('disabled', 'disabled');
        $("#user-member_cityid").attr('disabled', 'disabled');
        $("#user-member_areaid").attr('disabled', 'disabled');
    }

    /**
     * 模态框, 需要时开启
    $(".glyphicon-eye-open").click(function() {
        var id = $(this).parent().attr('rel');//获取父节点的rel属性值
        $("#logistics_route_id").val(id);

        var py = $(this).parent().parent().prev();//获取父节点的父节点的上一个兄弟节点

        $("#Modal").modal();
    });
     */

});

function handleDriver(condition){
    if(condition == 'add'){
        //添加
        $('#stype').val(condition);
        $('#addDriver').submit();

    }
    else if(condition == 'remove'){
        //删除
        $('#rtype').val(condition);
        $('#removeDriver').submit();

    }
}

$('#driver-driver_id').change(function () {
    var id = $('#driver-driver_id').val();
    if (!id) {
        $('#logistics_car_number').val('');
    }

    $.ajax({
        // url: '?r=addition/get-driver',//以前是根据车获取司机,现在不用了
        url: '?r=addition/get-car-number',
        type: 'post',
        async: true,
        data: {'id': id},
        dataType: 'json',
        success: function (res) {
            console.log(res.data);
            if (res.data) {
                $('#logistics_car_number').val(res.data);
            }
        }
    });

});

$('#pinyin_name').blur(function () {
    var pyVal = $('#help-block-pinyin_name').val();
    if (!pyVal) {
        var pyVal = $('#help-block-pinyin_name').html("拼音不能为空");
    }
});



$("#signupform-member_provinceid").change(function () {

    $('#pinyin_name').val('');
    $("#pinyin_name").attr('disabled', false);

});

$('#signupform-member_cityid').change(function () {

    var province_name = $('#signupform-member_provinceid').val();
    var pinyin_name = $('#signupform-member_cityid').val();

    if (pinyin_name) {
        //辽宁沈阳 不可编辑
        if (province_name == 6 && pinyin_name == 107) {

            $('#pinyin_name').val("");
            $("#pinyin_name").attr('disabled', true);

        }
        //其他情况可以编辑
        else {
            $('#pinyin_name').attr('disabled', false);

            var py = _fetchPy(pinyin_name);

        }
    }

});

function _fetchPy(pinyin_name) {
    $.ajax({
        url: '?r=addition/check-pin-yin',
        type: 'post',
        async: true,
        data: {'pinyin_name': pinyin_name},
        dataType: 'json',
        success: function (res) {
            if (res.data.pinyin_name) {
                $('#pinyin_name').val(res.data.pinyin_name);
                $("#pinyin_name").attr('disabled', true);
            }
        }
    });
}


//creation or modification
function setSubType(type) {
    var subType = $('#subType').val(type);

    if (subType) return true;

}
