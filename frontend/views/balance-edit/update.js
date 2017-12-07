$(function(){
	$('#logisticsorder-collection').change(function(){
		if($(this).val()==1){
			var true_price = $('#logisticsorder-goods_price').val();
			$('#true_price').val(true_price).removeAttr("readonly");
		}else{
			$('#true_price').val(0).attr({"readonly":"readonly"});
		}
	})
	
	$('#logisticsorder-add_order_sn').click(function(){
		$(getOrderSnHtml()).insertBefore($(this));
	})
	
	$('#search-form').autocomplete({
        ajaxUrl: '?r=area/search',
        width: 300,
        height: 23,
        onSubmit: function(obj) {
            selectCallBack(obj);
        }
    })
	$('#logisticsorder-logistics_route_id').change(function(){
		var data = {
			       'route_id':$(this).val(),
			   };
			   $.ajax({
			        type: "post",
			        url:'?r=employee/terminus-option',
			        data:data,
			        async:true,
			        dataType: 'json',
			        success:function(data){
			       	 console.log(data);
			       	 if(data.sameCity==1){
			       		 $('.terminusSelect').hide();
			                $('#logisticsorder-terminus_id').attr('disabled',true);
			       	 }else{
			       		 $('.terminusSelect').show();
			                $('#logisticsorder-terminus_id').attr('disabled',false);
			                $('#logisticsorder-terminus_id option').remove();
			                $('#logisticsorder-terminus_id').append(data.terminus_str); 
			               	 }
			                   
			               }
			       })
	})
})

function getOrderSnHtml(){
	var str = '';
    str += '<div class="table01">';
    str += '<div class="table_div"><label>订单编号:</label><input type="text" list="sn_list" class="form-control sn" name="OrderSn[]" onblur="javascript:GetValue(this.value);" onkeyup="javascript:SearchSn(this.value);"></div>';
    str += '<datalist id="sn_list" >';
	  
    str += '</datalist>';
	   str += '</div>';
     return str;
}

//物流回调函数
function selectCallBack(obj){
	if(obj.area_deep == '2') {
        $('#logisticsorder-receiving_cityid').val(obj.area_id);
        $('#logisticsorder-receiving_areaid').val(0);
    } else if(obj.area_deep == '3') {
        $('#logisticsorder-receiving_cityid').val(obj.area_parent_id);
        $('#logisticsorder-receiving_areaid').val(obj.area_id);
    }
	getRouteOption(obj);
}

function getRouteOption(obj){
    var data = {
            'city_id':obj.area_id,
            'city_type':obj.area_deep
        };
        $.ajax({
             type: "post",
             url:'?r=employee/route-option',
             data:data,
             async:true,
             dataType: 'json',
             success:function(data){
                $('#logisticsorder-logistics_route_id option').remove();
                $('#logisticsorder-logistics_route_id').append(data); 
                $('#logisticsorder-logistics_route_id').change();
            }
        })
}


//模糊查询订单号并显示在列表下
function SearchSn(no){
// console.log(no);
  $.ajax({
         type: "get",
         async: true,
		 data:{order_sn:no},
		 url:'https://www.youjian8.com/shop/index.php?act=logistics_order_list&op=searchordersn',
		 dataType: "jsonp",
         jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
         jsonpCallback:"flightHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
         success: function(json){	
			$('#sn_list').find("option").remove('');
			if(json!=null){
		    	for(var i=0;i<json.length;i++){
			  // console.log(json[i].order_sn);
			//  if(i==0 && $('#logisticsorder-order_sn').val().length!=''){
			//	  _showinput_order_sn();
			 // }
			  $('#sn_list').append(' <option label="订单编号" value="'+json[i].order_sn+'" />	');
			  }
			}
         },
         error: function(){
            console.log('fail');
			 return;
         }
         });
}
//物流单号失去焦点调用函数
//用途累加待付款金额
function GetValue(new_sn){
	var goods_price = 0;
	 var i=0;
	 var falg = true;
	 var str  = '';
	     //$('input[class="sn"]').each(function(index, element) {
		  for(var i=0;i<document.getElementsByName('OrderSn[]').length;i++){
			  if(document.getElementsByName('OrderSn[]').item(i).value!=''){
		         str+=document.getElementsByName('OrderSn[]').item(i).value+',';
			  }
		  }
		  str = str.substring(0,str.length-1);
		  if(str==''){
			 if($('#logisticsorder-collection').val()==1){
				 $('#true_price').val($('#logisticsorder-goods_price').val());
				 $('#true_price').attr('readonly',false);
			 }
			  return;
		  }
		$.ajax({
		    type: "get",
		    async: true,
			 data:{order_sn:str,type:'new_wuliu'},
			 url:'https://www.youjian8.com/shop/index.php?act=logistics_order_list&op=sendgoods',
			 dataType: "jsonp",
		    jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
		    jsonpCallback:"flightHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
		    success: function(json){
			 if(json==null){return;}
			 if($('#logisticsorder-collection').val()==2){return;}
			//累加代收货款
			 $('#true_price').val(json.goods_price);
			 $('#true_price').attr('readonly',true);
       },
       error: function(){
          console.log('fail');
			 return;
       }
       });
}