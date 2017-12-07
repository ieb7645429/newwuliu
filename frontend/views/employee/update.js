var employeeUpdate = {
    init : function() {
        this._setEvent();
        this._changeCollection();
        
        this.cityArea = $('#search-form').autocomplete({
            ajaxUrl: '?r=area/search',
            width: 300,
            height: 23,
            onSubmit: function(obj) {
                employeeUpdate._selectCallBack(obj);
            }
        });
    },
    _setEvent : function() {
        //$('#logisticsorder-member_phone').on('blur', this._memberPhoneBlurCallBack);
       // $('#user-username').on('blur', this._memberNameBlurCallBack);
		//$('#user-small_num').on('blur', this._memberSmallNameBlurCallBack);
       // $('#logisticsorder-receiving_phone').on('blur', this._getReceivingInfo);
       // $('#logisticsorder-order_type').on('change',this._showinput);
    	$('#user-username').on('change', this._memberNameBlurCallBack);
 		$('#user-small_num').on('blur', this._memberSmallNameBlurCallBack);
        $('#logisticsorder-receiving_phone').on('blur', this._getReceivingInfo);
 		$('#logisticsorder-member_name').on('blur', this._memberReceNameBlurCallBack);
 		$('#logisticsorder-member_phone').on('blur', this._memberRecePhoneBlurCallBack);	
    	
  
        //$('#logisticsorder-collection').on('change',this._showinput_collection);
		//$('#logisticsorder-order_sn').on('blur',this._showinput_order_sn);
		$('#logisticsorder-add_goods_price').on('click', this._addGoosInfoClickCallBack);
		$('#logisticsorder-add_order_sn').on('click', this._addOrderSnClickCallBack);
		$('#logisticsorder-collection').on('change',this._changeCollection);
//		$('#logisticsorder-receiving_cityid').on('change',this._getRouteOption);
		$('#logisticsorder-logistics_route_id').on('change',this._getTerminusOption);
		$('#user-username').on('blur',this._userNameExperimental);
		$('#user-username').on('focus',this._unUserNameExperimental);	
		$('#user-small_num').on('blur',this.userNameExperimental);
		$('#user-small_num').on('focus',this._unUserNameExperimental);	
    },
    
    _selectCallBack: function(obj) {
        if(obj.area_deep == '2') {
            $('#logisticsorder-receiving_cityid').val(obj.area_id);
            $('#logisticsorder-receiving_areaid').val(0);
        } else if(obj.area_deep == '3') {
            // $('#logisticsorder-receiving_cityid').val(obj.area_parent_id);
            // $('#logisticsorder-receiving_areaid').val(obj.area_id);
            $('#logisticsorder-receiving_cityid').val(obj.area_id);
        }
        employeeUpdate._getRouteOption(obj);
    },
    
    _memberPhoneBlurCallBack : function() {
        if($('#logisticsorder-member_phone').val()) {
            $.post(
                    $('#memberInfoUrl').val() , 
                    {phone: $('#logisticsorder-member_phone').val()},
                    function(data){
                        if(data.code == 200 && data.datas) {
                            $('#user-username').val(data.datas.username);
                            $('#logisticsorder-member_name').val(data.datas.user_truename);
                            $('#logisticsorder-member_cityid').val(data.datas.member_cityid);
                            $('#logisticsorder-member_cityid').change();
                        }
                    },
                    'json'
                );
        }
    },
    _memberNameBlurCallBack : function(){
  	  //判断是否状态为1，若为1不调用
      	if($('#user-username').val()) {
              //获取ajax
  			//type 来源
  			getMemberInfo('name');
          }
      },
  	 _memberSmallNameBlurCallBack : function(){
      	if($('#user-small_num').val()) {
             getMemberInfo('smallnum');
          }
      },
  	 _memberReceNameBlurCallBack : function(){
  		
      	if($('#logisticsorder-member_name').val()) {
             getMemberInfo('membername');
          }
      },
  	 _memberRecePhoneBlurCallBack : function(){
      	if($('#logisticsorder-member_phone').val()) {
             getMemberInfo('phone');
          }
      },
    
    _getReceivingInfo : function() {
        if($('#logisticsorder-receiving_phone').val()) {
            $.post(
                    $('#receivingUrl').val() , 
                    {phone: $('#logisticsorder-receiving_phone').val()},
                    function(data){
                        if(data.code == 200 && data.datas) {
                        	if(data.code == 200 && data.datas) {
                                //如果为空,则不显示; 不为空0
                                $('#logisticsorder-return_goods_rate').text('返货率: ' + data.datas.rate);//返货率

                                if(data.datas.area_name) {
                                    $(employeeUpdate.cityArea).find("input").val(data.datas.info.area_name);
//                                    employeeCreate._getRouteOption({area_id: data.datas.area_id, area_deep: 3});
                                } else {
                                    $(employeeUpdate.cityArea).find("input").val(data.datas.info.city_name);
//                                    employeeCreate._getRouteOption({area_id: data.datas.city_id, area_deep: 2});
                                }
                                // 更新落地点下拉
                                $('#logisticsorder-logistics_route_id option').remove();
                                $('#logisticsorder-logistics_route_id').append(data.datas.route_str);
                                
                                if(data.datas.info.logistics_route_id != '0') {
                                    $('#logisticsorder-logistics_route_id').val(data.datas.info.logistics_route_id);
                                    if(data.datas.terminus_str) {
                                        $('.terminus_list').show();
                                        $('#logisticsorder-terminus_id').attr('disabled',false);
                                        $('#logisticsorder-terminus_id option').remove();
                                        $('#logisticsorder-terminus_id').append(data.datas.terminus_str);
                                        if(data.datas.info.terminus_id != '0') {
                                            $('#logisticsorder-terminus_id').val(data.datas.info.terminus_id); 
                                        }
                                    } else {
                                        $('#logisticsorder-logistics_route_id').change();
                                    }
                                    
                                } else {
                                    $('#logisticsorder-logistics_route_id').change();
                                }
                                
                                $('#logisticsorder-receiving_name').val(data.datas.info.name);
                                $('#logisticsorder-receiving_cityid').val(data.datas.info.city_id);
                                $('#logisticsorder-receiving_areaid').val(data.datas.info.area_id);
                                $('#logisticsorder-receiving_name_area').val(data.datas.info.area_info);
                        	}
    						else{
//    						  alert(data.msg);
                             //// if($('#status').val()==1){
    				   	     //    $('#logisticsorder-receiving_phone').val('');
    						     $('#logisticsorder-receiving_name').val('');
    						     $('#logisticsorder-receiving_name_area').val('');
                                 $('#logisticsorder-return_goods_rate').text('');
    						//  }

                            }
                        }
                    },
                    'json'
                );
        }
    },
    _showinput : function(){
        if($('#logisticsorder-order_type option:selected').val() == 3 || $('#logisticsorder-order_type option:selected').val() == 5) {
           $('#order_sn').show();
           $('#logisticsorder-order_sn').attr("disabled",false);
        }
        else{
            $('#order_sn').hide();
            $('#logisticsorder-order_sn').attr("disabled",true);
        }
    },
    _showinput_collection : function(){
        if($('#logisticsorder-collection option:selected').val() == 1) {
            $('#collection').show();
            $('#logisticsorder-collection_poundage_one').attr("disabled",false);
            $('#logisticsorder-collection_poundage_two').attr("disabled",false);
         }
         else{
            $('#collection').hide();
            $('#logisticsorder-collection_poundage_one').attr("disabled",true);
            $('#logisticsorder-collection_poundage_two').attr("disabled",true);
         }
      },
      _showinput_order_sn : function(){
          if($('#logisticsorder-order_type option:selected').val() == 5)
   	   {
   		  // console.log('http://pre.youjian8.com/shop/index.php?act=logistics_order_list&op=sendgoods?order_sn='+$('#logisticsorder-order_sn').val()+'&type=wuliu');
   		 //  return;
   			 $.ajax({
                type: "get",
                async: true,
   			 data:{order_sn:$('#logisticsorder-order_sn').val(),type:'wuliu'},
   			 url:'http://www.youjian8.com/shop/index.php?act=logistics_order_list&op=sendgoods',
   			 dataType: "jsonp",
                jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
                jsonpCallback:"flightHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
                success: function(json){
   			   $("#logisticsorder-member_cityid option[value='"+json.member_cityid+"']").attr("selected", true);
   			   $('#logisticsorder-member_name').val(json.member_name);
                  $('#logisticsorder-member_phone').val(json.member_phone);
   			   $('#logisticsorder-goods_price').val(json.goods_price);
   			   $('#logisticsorder-receiving_phone').val(json.receiving_phone);
   			   $('#logisticsorder-receiving_name').val(json.receiving_name);
   			   $('#logisticsorder-receiving_name_area').val(json.receiving_name_area);
   			   if(json.receiving_provinceid!=0 || json.receiving_provinceid==6){
   			    $("#logisticsorder-receiving_provinceid option[value='"+json.receiving_provinceid+"']").attr("selected", true);
   			   }
   			    $("#logisticsorder-receiving_cityid option[value='"+json.receiving_cityid+"']").attr("selected", true);
   			    $("#logisticsorder-receiving_areaid option[value='"+json.receiving_areaid+"']").attr("selected", true);
                },
                error: function(){
                   console.log('fail');
   				 return;
                }
                });
          }
     },
	 _addOrderSnClickCallBack : function(){
	   $(employeeUpdate._getOrderSnHtml()).insertBefore($(this));
	},
    _createGoodsInfo : function() {
        var div = $('<div class="form-group field-info-goods_info has-success"></div>');
        div.append('<label class="control-label" for="logisticsorder-goods_price">商品信息</label>');
        div.append(employeeUpdate._getGoodsInfoHtml());
        var btn = $('<a href="" class="btn btn-success" onclick="return false;">添加商品</a>');
        btn.click(employeeUpdate._addGoosInfoClickCallBack);
        div.append(btn);
        div.append('<div class="help-block"></div>');
        div.insertAfter($('#logisticsorder-return_all').parent());
    },
    
    _getGoodsInfoHtml : function() {
        var str = '';
        str += '<div class="table01">';
        str += '<div class="table_div">商品名称:<input type="text" class="form-control" name="GoodsInfo[name][]"></div>';
        str += '<div class="table_div">商品数量:<input type="text" class="form-control" name="GoodsInfo[number][]"></div>';
        str += '<div class="table_div">商品价钱:<input type="text" class="form-control" name="GoodsInfo[price][]"></div>';
        str += '</div>';
        return str;
    },
     _getOrderSnHtml : function() {
	   var str = '';
       str += '<div class="table01">';
       str += '<div class="table_div">订单编号:<input type="text" list="sn_list" class="form-control" name="OrderSn[]" onblur="javascript:GetValue(this.value);" onkeyup="javascript:SearchSn(this.value);"></div>';
       str += '<datalist id="sn_list" >';
	  
       str += '</datalist>';
	   str += '</div>';
        return str;
	},
    _addGoosInfoClickCallBack : function() {
        $(employeeUpdate._getGoodsInfoHtml()).insertBefore($(this));
    },
    _changeCollection : function(){
        if($('#logisticsorder-collection').val()==1){
           $('.no-charge').show();
           if($('#save_price').val()){
               $('#logisticsorder-goods_price').val($('#save_price').val());
           }
//           if(!isExistOption('logisticsorder-shipping_type',2)){
//               $("#logisticsorder-shipping_type").append("<option value='2'>回付</option>"); 
//           }
//           $('#logisticsorder-shipping_type option[value="3"]').remove();
        }else if($('#logisticsorder-collection').val()==2){
           $('.no-charge').hide();
           $('#save_price').val($('#logisticsorder-goods_price').val());
           $('#logisticsorder-goods_price').val('0');
//           if(!isExistOption('logisticsorder-shipping_type',3)){
//               $("#logisticsorder-shipping_type").append("<option value='3'>已付</option>");
//           }
//           $('#logisticsorder-shipping_type option[value="2"]').remove();
        }
   },
   _getRouteOption : function(obj){
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
   },
   _getTerminusOption : function(){
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
   },
   _userNameExperimental : function(){
	   userNameExperimental();
   },
   _unUserNameExperimental : function() {
   	var userNameHelp = $('.field-user-username');
   	userNameHelp.find('.help-block').hide();
   	if(userNameHelp.find('.username-block').length==0){
   		userNameHelp.find('.help-block').after('<div class="username-block"></div>');
   	}
	//新增加小号
		var userNameHelp = $('.field-user-small_num');
    	userNameHelp.find('.help-block').hide();
    	if(userNameHelp.find('.small_num-block').length==0){
    		userNameHelp.find('.help-block').after('<div class="small_num-block"></div>');
    	}
  //焦点阴影
	 	if($('#user-small_num').val()!=''){
	    	if(userNameExperimental()){
	    		$('#user-small_num').css({'box-shadow': 'inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #67b168'});
	    	}else{
	    		$('#user-small_num').css({'box-shadow': 'inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 6px #ce8483'});
	    	}
    	}
   }
}

$(document).ready(function() {
    employeeUpdate.init();
});
//用户名验证效果
function userNameExperimental(){
	var userName = $('#user-username').val();
	var usersmallNum =$('#user-small_num').val().trim().replace(/\s/g," ");
	var pattern = /^1[34578]\d{9}$/; 
		if(pattern.test(userName)==false && usersmallNum.length==0){
			userNameWorng();
			return false;
		}else{
			userNameRight();
			return true;
		}
}

//$('#user-username').val().trim().replace(/\s/g," ")
function userNameWorng(){
	//$('.field-user-username').removeClass('has-success').addClass('has-error');
	$('.field-user-username').find('.control-label').css({"color": "#a94442"});
	$('.field-user-username').find('.form-control').css({"border-color": "#a94442"}); 
	$('.field-user-username').find('.username-block').css({"color": "#a94442"}).html('会员号必须为电话号码');
	$('#user-username').css({'box-shadow': 'inset 0 0px 0px rgba(0, 0, 0, 0), 0 0 6px #fff'});
	//新增加小号效果
	$('.field-user-small_num').find('.control-label').css({"color": "#a94442"});
	$('.field-user-small_num').find('.form-control').css({"border-color": "#a94442"}); 
	$('.field-user-small_num').find('.small_num-block').css({"color": "#a94442"}).html('会员小号不能为空');
	$('#user-small_num').css({'box-shadow': 'inset 0 0px 0px rgba(0, 0, 0, 0), 0 0 6px #fff'});
}
function userNameRight(){
	//$('.field-user-username').removeClass('has-error').addClass('has-success');
	$('.field-user-username').find('.control-label').css({"color": "#3c763d"});
	$('.field-user-username').find('.form-control').css({"border-color": "#3c763d"});
	$('.field-user-username').find('.username-block').css({"color": "#3c763d"}).html('');
	$('#user-username').css({'box-shadow': 'inset 0 0px 0px rgba(0, 0, 0, 0), 0 0 6px #fff'});
	//新增加小号效果
	$('.field-user-small_num').find('.control-label').css({"color": "#3c763d"});
	$('.field-user-small_num').find('.form-control').css({"border-color": "#3c763d"});
	$('.field-user-small_num').find('.small_num-block').css({"color": "#3c763d"}).html('');
	$('#user-small_num').css({'box-shadow': 'inset 0 0px 0px rgba(0, 0, 0, 0), 0 0 6px #fff'});
}
function Check_GoodsInfo(){
	return userNameExperimental();
    var flag = false;
	//console.log($('input[name="GoodsInfo[name][]"]').length);
	$('input[name="GoodsInfo[name][]"]').each(function(index, element) {
	//  console.log($('input[name="GoodsInfo[number][]"]').eq(index).val());
	  if($(this).val()=='' || $('input[name="GoodsInfo[number][]"]').eq(index).val()=='' 
	  || $('input[name="GoodsInfo[price][]"]').eq(index).val()=='')
	  {
	  if($(this).val()=='' &&
		 $('input[name="GoodsInfo[number][]"]').eq(index).val()==''&&
	     $('input[name="GoodsInfo[price][]"]').eq(index).val()=='')
	  {
	   $(this).attr('style','');
	   $('input[name="GoodsInfo[number][]"]').eq(index).attr('style','');
	   $('input[name="GoodsInfo[price][]"]').eq(index).attr('style','');
	   flag = true;
	  }else{
		  $(this).attr('style','border:1px solid #ff0000');
		  $('input[name="GoodsInfo[number][]"]').eq(index).attr('style','border:1px solid #ff0000');
		  $('input[name="GoodsInfo[price][]"]').eq(index).attr('style','border:1px solid #ff0000');
		   console.log('fail');
	  	}
	  }
	  else{
	   $(this).attr('style','');
	   $('input[name="GoodsInfo[number][]"]').eq(index).attr('style','');
	   $('input[name="GoodsInfo[price][]"]').eq(index).attr('style','');
	   flag = true;
	  }
	});
	   return flag;
}
//判断select中是否存在值为value的项  
function isExistOption(id,value) {  
    var isExist = false;  
     var count = $('#'+id).find('option').length;     
      for(var i=0;i<count;i++)     
      {     
         if($('#'+id).get(0).options[i].value == value)     
             {     
                   isExist = true;     
                        break;     
                  }     
        }     
        return isExist;  
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
		// var new_sn = //$('.sn').val().replace(/，/g,",");
		 //判断是否包含逗号
		/* if(new_sn.indexOf(',')>0){
		   array_sn = new_sn.split(',');
		   new_sn   = array_sn[array_sn.length-1];
		 }*/
 var str  = '';
 for(var i=0;i<document.getElementsByName('OrderSn[]').length;i++){
     if(document.getElementsByName('OrderSn[]').item(i).value!=''){
        str+=document.getElementsByName('OrderSn[]').item(i).value+',';
     }
 }
 str = str.substring(0,str.length-1);
 if(str==''){
    $('#user-username').val('');//新添加到会员号
    $('#user-small_num').val('');
    $('#logisticsorder-member_name').val('');
    $('#logisticsorder-member_phone').val('');
    $('#logisticsorder-receiving_phone').val('');
    $('#logisticsorder-receiving_name').val('');
    $('#logisticsorder-receiving_name_area').val('');
    $('#logisticsorder-goods_price').val(''); 
    $('#logisticsorder-goods_price').attr('readonly',false);
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
		 /*  $("#logisticsorder-member_cityid option[value='"+json.member_cityid+"']").attr("selected", true);
		   $('#logisticsorder-member_name').val(json.member_name);		 
           $('#logisticsorder-member_phone').val(json.member_phone);
		   $('#logisticsorder-goods_price').val(json.goods_price);
		   $('#logisticsorder-receiving_phone').val(json.receiving_phone);
		  
		   $('#logisticsorder-receiving_name_area').val(json.receiving_name_area);
		   if(json.receiving_provinceid!=0 || json.receiving_provinceid==6){
		    $("#logisticsorder-receiving_provinceid option[value='"+json.receiving_provinceid+"']").attr("selected", true);
		   }
		    $("#logisticsorder-receiving_cityid option[value='"+json.receiving_cityid+"']").attr("selected", true);
		    $("#logisticsorder-receiving_areaid option[value='"+json.receiving_areaid+"']").attr("selected", true);*/
//			if($('#user-username').val().length==0){
			$('#user-username').val(json.member_phone);//新添加到会员号
			$('#user-username').change();
			$('#logisticsorder-receiving_phone').val(json.receiving_phone);
			$('#logisticsorder-receiving_name').val(json.receiving_name);
			$('#logisticsorder-receiving_name_area').val(json.receiving_name_area);
		   // $('#logisticsorder-receiving_phone').change();
//			}
			//累加代收货款
//			if($('#logisticsorder-goods_price').val().length>0){
//			  goods_price =$('#logisticsorder-goods_price').val();
//			}
//			var new_goods_price=parseFloat(goods_price)+parseFloat(json.goods_price);
//			$('#logisticsorder-goods_price').val(new_goods_price);
			//改为代收状态
			$('#logisticsorder-collection').val(1);
			$('.no-charge').show();
			$('#logisticsorder-goods_price').val(json.goods_price);
            $('#logisticsorder-goods_price').attr('readonly',true);
         },
         error: function(){
            console.log('fail');
			 return;
         }
         });
		
}
//获取ajax来源
function getMemberInfo(type){
	// if($('#status').val()==1){return;}
	  var text = '';
         switch(type){
		    case 'name':
			  var data = { 'name': $('#user-username').val(),};
			break;
			case 'smallnum':
			  var data = { 'smallname': $('#user-small_num').val(),};
			break;
			case 'phone':
			  var data = { 'phone': $('#logisticsorder-member_phone').val(),};
			break;
			case 'membername':
			 if($('#logisticsorder-member_name').attr('data-id').length==0){
			     var data = { 'membername': $('#logisticsorder-member_name').val(),};
			 }
			 else{
			      var data = { 'membername': $('#logisticsorder-member_name').attr('data-id'),};
			}
			break;
		 }
                $.post(
                     $('#memberInfoUrl').val(), 
                     data,				  
                    function(data){
                   // console.log(data);
                        if(data.code == 200 && data.datas) {
							$('#user-username').val(data.datas.username);
                            $('#logisticsorder-member_phone').val(data.datas.member_phone);
                            $('#logisticsorder-member_name').val(data.datas.user_truename);
							$('#logisticsorder-member_name').attr('data-id',data.datas.id);
							$('#user-small_num').val(data.datas.small_num);
                            $('#logisticsorder-member_cityid').val(data.datas.member_cityid);
                            $('#logisticsorder-member_cityid').change();
							//只允许调用一次
							$('#status').val('1');
                        }
						else{
						    $('#user-username').val('');
                            $('#logisticsorder-member_phone').val('');
                            $('#logisticsorder-member_name').val('');
							$('#user-small_num').val('');
						}
                    },
                    'json'
                );
}
//模糊查发货人姓名并显示在列表下
function SearchMemberName(Name){
		    var data = {
                'member_name':Name,
            };
		    $('#member_name_list').find("option").remove('');
			　var reg = new RegExp("[\\u4E00-\\u9FFF]+","g");
　         　 if(!reg.test(Name)){return;}
            $.ajax({
                 type: "post",
                 url:'?r=employee/member-name-info',
                 data:data,
                 async:true,
                 dataType: 'json',
                 success:function(data){
                	if(data.code==200){
						for(var i=0;i<data.datas.length;i++){
					       $('#member_name_list').append('<option value="'+data.datas[i].user_truename+'"></option>');
						  //  console.log(data.datas[i].user_truename);
					  }
					}
                    
                }
            })
}
//提交表单
$('#submitButton').click(function(){
	$(this).attr('disabled','disabled');
	if(!confirm("是否确定收货？")){
		$(this).attr('disabled',false);
	}else{
		if($('#user-username').val()==''){
			$(this).attr('disabled',false);
		}
		if($('#logisticsorder-member_name').val()==''){
			$(this).attr('disabled',false);
		}
		if($('#logisticsorder-member_phone').val()==''){
			$(this).attr('disabled',false);
		}
		if($('#logisticsorder-goods_num').val()==''){
			$(this).attr('disabled',false);
		}
		if($('#logisticsorder-receiving_phone').val()==''){
			$(this).attr('disabled',false);
		}
		if($('#logisticsorder-receiving_name').val()==''){
			$(this).attr('disabled',false);
		}
		if($('#logisticsorder-logistics_route_id').val()==''){
			$(this).attr('disabled',false);
		}
		if($('#logisticsorder-goods_price').val()==''){
			$(this).attr('disabled',false);
		}
		if($('#logisticsorder-freight').val()==''){
			$(this).attr('disabled',false);
		}
		$('#w0').submit();
	}
})