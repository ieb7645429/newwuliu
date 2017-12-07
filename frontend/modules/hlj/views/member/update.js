var Update = {
    init : function() {
        this._setEvent();
        this._changeCollection();
    },
    
    _setEvent : function() {
        $('#logisticsorder-receiving_phone').on('blur', this._getReceivingInfo);
		$('#logisticsorder-add_goods_price').on('click', this._addGoosInfoClickCallBack);
		$('#logisticsorder-collection').on('change', this._changeCollection)
    },
    
    _getReceivingInfo : function() {
        if($('#logisticsorder-receiving_phone').val()) {
            $.post(
                    $('#receivingUrl').val(), 
                    {phone: $('#logisticsorder-receiving_phone').val()},
                    function(data){
                        if(data.code == 200 && data.datas) {
                            $('#logisticsorder-receiving_name').val(data.datas.name);
                            $('#logisticsorder-receiving_cityid').val(data.datas.city_id);
                            $('#logisticsorder-receiving_cityid').change();
                            $('#logisticsorder-receiving_areaid').val(data.datas.area_id);
                            $('#logisticsorder-receiving_name_area').val(data.datas.area_info);
                        }
                    },
                    'json'
                );
        }
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
    
    _addGoosInfoClickCallBack : function() {
        $(Update._getGoodsInfoHtml()).insertBefore($(this));
    },
    _changeCollection : function(){
        if($('#logisticsorder-collection').val()==1){
           $('.no-charge').show();
           if(!isExistOption('logisticsorder-shipping_type',2)){
               $("#logisticsorder-shipping_type").append("<option value='2'>回付</option>"); 
           }
           $('#logisticsorder-shipping_type option[value="3"]').remove();
        }else if($('#logisticsorder-collection').val()==2){
           $('.no-charge').hide();
           if(!isExistOption('logisticsorder-shipping_type',3)){
               $("#logisticsorder-shipping_type").append("<option value='3'>已付</option>");
           }
           $('#logisticsorder-shipping_type option[value="2"]').remove();
        }
   },
};

$(document).ready(function() {
    Update.init();
});
function Check_GoodsInfo(){
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
