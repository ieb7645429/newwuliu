var returnCreate = {
    init : function() {
        this._setEvent();
        this._changeCollection();
    },
    
    _setEvent : function() {
        $('#logisticsreturnorder-member_phone').on('blur', this._getReceivingInfo);
        $('#logisticsreturnorder-receiving_phone').on('blur', this._memberPhoneBlurCallBack);
        $('#logisticsreturnorder-collection').on('change',this._changeCollection);
        $('#logisticsreturnorder-add_goods_price').on('click', this._addGoosInfoClickCallBack);
    },

    _memberPhoneBlurCallBack : function() {
        if($('#logisticsreturnorder-receiving_phone').val()) {
            $.post(
                    $('#memberInfoUrl').val() , 
                    {phone: $('#logisticsreturnorder-receiving_phone').val()},
                    function(data){
                        if(data.code == 200 && data.datas) {
                            $('#logisticsreturnorder-receiving_name').val(data.datas.user_truename);
                            $('#logisticsreturnorder-receiving_cityid').val(data.datas.member_cityid);
                            $('#logisticsreturnorder-receiving_cityid').change();
                            $('#logisticsreturnorder-receiving_areaid').val(data.datas.member_areaid);
                            $('#logisticsreturnorder-receiving_name_area').val(data.datas.member_areainfo);
                        }
                    },
                    'json'
                );
        }
    },

    _getReceivingInfo : function() {
        if($('#logisticsreturnorder-member_phone').val()) {
            $.post(
                    $('#receivingUrl').val() , 
                    {phone: $('#logisticsreturnorder-member_phone').val()},
                    function(data){
                        if(data.code == 200 && data.datas) {
                            $('#logisticsreturnorder-member_name').val(data.datas.name);
                            $('#logisticsreturnorder-member_cityid').val(data.datas.city_id);
                        }
                    },
                    'json'
                );
        }
    },
    
    _getGoodsInfoHtml : function() {
    	var str = '';
        str += '<div class="table01">';
        str += '<div class="table_div">商品名称:<input type="text" class="form-control" name="ReturnInfo[name][]"></div>';
        str += '<div class="table_div">商品数量:<input type="text" class="form-control" name="ReturnInfo[number][]"></div>';
        str += '<div class="table_div">商品价钱:<input type="text" class="form-control" name="ReturnInfo[price][]"></div>';
        str += '</div>';
        return str;
    },
    _changeCollection : function(){
        if($('#logisticsreturnorder-collection').val()==1){
           $('.no-charge').show();
           if(!isExistOption('logisticsreturnorder-shipping_type',2)){
               $("#logisticsreturnorder-shipping_type").append("<option value='2'>回付</option>"); 
           }
           $('#logisticsreturnorder-shipping_type option[value="3"]').remove();
        }else if($('#logisticsreturnorder-collection').val()==2){
           $('.no-charge').hide();
           if(!isExistOption('logisticsreturnorder-shipping_type',3)){
               $("#logisticsreturnorder-shipping_type").append("<option value='3'>已付</option>");
           }
           $('#logisticsreturnorder-shipping_type option[value="2"]').remove();
        }
   },
   _getGoodsInfoHtml : function() {
   	var str = '';
       str += '<div class="table01">';
       str += '<div class="table_div">商品名称:<input type="text" class="form-control" name="ReturnInfo[name][]"></div>';
       str += '<div class="table_div">商品数量:<input type="text" class="form-control" name="ReturnInfo[number][]"></div>';
       str += '<div class="table_div">商品价钱:<input type="text" class="form-control" name="ReturnInfo[price][]"></div>';
       str += '</div>';
       return str;
   },
    
    _addGoosInfoClickCallBack : function() {
        $(returnCreate._getGoodsInfoHtml()).insertBefore($(this));
    }
};

$(document).ready(function() {
    returnCreate.init();
});
function Check_GoodsInfo(){
    var flag = false;
    if($('#logisticsreturnorder-return_all').val()==1){
    	flag = true;
    }
	//console.log($('input[name="GoodsInfo[name][]"]').length);
	$('input[name="ReturnInfo[name][]"]').each(function(index, element) {
	//  console.log($('input[name="GoodsInfo[number][]"]').eq(index).val());
	  if($(this).val()=='' || $('input[name="GoodsInfo[number][]"]').eq(index).val()=='' 
	  || $('input[name="ReturnInfo[price][]"]').eq(index).val()=='')
	  {
	  if($(this).val()=='' &&
		 $('input[name="ReturnInfo[number][]"]').eq(index).val()==''&&
	     $('input[name="ReturnInfo[price][]"]').eq(index).val()=='')
	  {
	   $(this).attr('style','');
	   $('input[name="ReturnInfo[number][]"]').eq(index).attr('style','');
	   $('input[name="ReturnInfo[price][]"]').eq(index).attr('style','');
	   flag = true;
	  }else{
		  $(this).attr('style','border:1px solid #ff0000');
		  $('input[name="ReturnInfo[number][]"]').eq(index).attr('style','border:1px solid #ff0000');
		  $('input[name="ReturnInfo[price][]"]').eq(index).attr('style','border:1px solid #ff0000');
		   console.log('fail');
	    }
	  }
	  else{
	   $(this).attr('style','');
	   $('input[name="ReturnInfo[number][]"]').eq(index).attr('style','');
	   $('input[name="ReturnInfo[price][]"]').eq(index).attr('style','');
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
