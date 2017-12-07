$(function(){
	$('#logisticsroute-logistics_route_id').change(function(){
        var data = {
            'route_id':$(this).val()
        };
        $.ajax({
             type: "post",
             url:'?r=dl/driver-manager/ajax-get-driver-list',
             data:data,
             async:true,
             success:function(data){
                 $('#logisticsorder-driver_member_id option:not(:first)').remove();
                 $('#logisticsorder-driver_member_id').append(data);
              }
            })
	})	
})