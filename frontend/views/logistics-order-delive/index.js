$('.js-print1').click(function(){
	
	var status = document.getElementById("status").value;
	var driver_member_id = $(this).data('driver');	
    var condition_time_by = document.getElementById("logisticsordersearch-condition_time_by").value;
    var condition_time = document.getElementById("logisticsordersearch-condition_time").value;
    var  RandomUrl  = window.location.href.split("frontend")[0];
    window.location.href=RandomUrl+"frontend/web/index.php?r=logistics-order-delive/view1&condition_time_by="+condition_time_by+"&condition_time="+condition_time+"&driver_member_id="+driver_member_id+"&status="+status;
//    die();
});


$('#searchButtonq').click(function(){
  	$("#download_type").val('0');
    $('#w0').attr('target', "_self");
    $('#w0').submit();
});


$('#downloadExcel').click(function(){
	$("#download_type").val('1');
	$('#w0').attr('target', "_blank");
	$('#w0').submit();
});
