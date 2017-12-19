$(document).ready(function () {

});


$('#logisticsroute-logistics_route_name').blur(function(){
   var logistics_route_name = $('#logisticsroute-logistics_route_name').val();

    $.ajax({
        url: '?r=addition/get-route',
        type: 'post',
        async: false,
        data: {'logistics_route_name': logistics_route_name},
        dataType: 'json',
        success: function (res) {
            if (res.data) {
                if(res.data.same_city == 1){

                    $('#logisticscar-car_type_id').html("<option value="+res.data.same_city+">是</option>");
                }
                else{
                    $('#logisticscar-car_type_id').html("<option value="+res.data.same_city+">否</option>");
                }

                $('#logistics_route_id').val(res.data.logistics_route_id);

            }

        }
    });

});


//应该是没用了, 过几天没事了可以删掉
// $('#car_name').blur(function(){
//     var car_number = $('#car_name').val();
//     if(car_number){
//
//         $.ajax({
//             url: '?r=addition/get-car',
//             type: 'post',
//             async: false,
//             data: {'car_number': car_number},
//             dataType: 'json',
//             success: function (res) {
//                 $('#logistics_car_id').val(res.data.logistics_car_id);
//             }
//         });
//     }
//
//
// });




$("#logisticsarea-province_id").change(function () {

    $('#logisticsroute-pinyin_name').val('');
    $("#logisticsroute-pinyin_name").attr('disabled', false);

});

$('#logisticsarea-city_id').change(function () {

    var province_name = $('#logisticsarea-province_id').val();
    var pinyin_name = $('#logisticsarea-city_id').val();

    if (pinyin_name) {
        //辽宁沈阳 不可编辑
        if (province_name == 6 && pinyin_name == 107) {

            $('#logisticsroute-pinyin_name').val("");
            $("#logisticsroute-pinyin_name").attr('disabled', true);

        }
        //其他情况可以编辑
        else {
            $('#logisticsroute-pinyin_name').attr('disabled', false);

            var py = _fetchPy(pinyin_name);

        }
    }

});

$('#logisticsarea-area_id').change(function(){
    //区变化时候
    $('#logisticsroute-pinyin_name').val("");
    $("#logisticsroute-pinyin_name").attr('disabled', false);

    var province_id = $('#logisticsarea-province_id').val();
    var city_id = $('#logisticsarea-city_id').val();
    var pinyin_name = $('#logisticsarea-area_id').val();
    if (pinyin_name) {
        var py = _fetchPy(pinyin_name);
    }






});

function _fetchPy(pinyin_name) {
    $.ajax({
        url: '?r=addition/check-pin-yin',
        type: 'post',
        async: false,
        data: {'pinyin_name': pinyin_name},
        dataType: 'json',
        success: function (res) {

            if (res.data.pinyin_name) {
                $('#logisticsroute-pinyin_name').val(res.data.pinyin_name);
                $("#logisticsroute-pinyin_name").attr('disabled', true);
            }
            else{
                $('#logisticsroute-pinyin_name').val('');
                $("#logisticsroute-pinyin_name").attr('disabled', false);
            }
        }
    });
}


//creation or modification
function setSubType(type) {
    var subType = $('#subType').val(type);

    if (subType) return true;

}

