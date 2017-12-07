var returnCreate = {
    init : function() {
        this._setEvent();
    },
    
    _setEvent : function() {
        $('#logisticsreturnorder-member_phone').on('blur', this._getReceivingInfo);
        $('#logisticsreturnorder-receiving_phone').on('blur', this._memberPhoneBlurCallBack);
        //$('#logisticsreturnorder-return_all').on('change', this._returnAllChangeCallBack)
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

    _returnAllChangeCallBack : function() {
        if($('#logisticsreturnorder-return_all').val() == 2) {
            returnCreate._createGoodsInfo();
            $('.field-logisticsreturnorder-goods_price').hide();
        } else {
            $('.field-returninfo-goods_info').html('');
            $('.field-logisticsreturnorder-goods_price').show();
        }
    },
    
    _createGoodsInfo : function() {
        var div = $('<div class="form-group field-returninfo-goods_info"></div>');
        div.append('<div class="title_bg"><span>退货商品信息</span></div>');
        div.append(returnCreate._getGoodsInfoHtml());
        var btn = $('<a href="" class="btn btn-success add-button" onclick="return false;">添加商品</a>');
        btn.click(returnCreate._addGoosInfoClickCallBack);
        div.append(btn);
        div.append('<div class="help-block"></div>');
        div.insertAfter($('#goodInfo'));
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

