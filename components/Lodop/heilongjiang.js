/**
* 打印功能
* xiaoyu
* 2017-07-20
**/
$(function(){
   // var LODOP = null;
       // if(LODOP == null){
		   if (needCLodop()) {
			window.On_CLodop_Opened=function(){
			 LODOP=getLodop();	//调用打印方法打印，详见样例22	
			window.On_CLodop_Opened=null;
			};	
			} else 
			window.onload = function(){ LODOP=getLodop();
			}
					   
       // }
      //  gerPrinter();
});
  
	/**
	*  黑龙江打印开单
	*  type为3是发货 1是返货，2是退货
	**/
	function printKD_HLJ(data,type) {
		 var type = arguments[1] ? arguments[1] : '';
		 LODOP.PRINT_INIT("黑龙江打印开单存根");
		 LODOP.SET_PRINT_STYLE("FontSize",8);
		 LODOP.SET_PRINT_COPIES(1);//打印1份
		 LODOP.SET_PRINT_PAGESIZE(0,'297mm',0,'黑龙江打印开单存根');
		 LODOP.SET_PRINT_STYLE("Bold", 1);
		 LODOP.ADD_PRINT_HTM(0,'30mm','72mm','297mm',KD_Style_HLJ(data,type));
		 LODOP.SET_PRINTER_INDEX('WL_CUNGEN'); //打印机名称需提前统一并且设置好 
	    // LODOP.PREVIEW();
	     LODOP.PRINT(); 
    }
	

/**
*  黑龙江
*  2017-10-19
*  @author:xiaoyu
**/
//开单存根 
	function KD_Style_HLJ(data,type){
	 var text   = '';
	 var _title = '';
	 var title = '友件网物流收货单';
	 var Name_1= '收货人';
	 var Name_2= '发货人';
	 var Name_3= '代收款';
	 if(type==2){
	   title = '友件网物流退货单';
	   Name_1= '收货人';
	   Name_2= '退货人';
	   Name_3= '退货货值';
	 }
	 if(type==1){
	   title = '友件网物流返货单';
	   Name_1= '收货人';
	   Name_2= '返货人';
	   Name_3= '返货货值';
	 }
	 if(type==3){
	   return KD_Style_1(data);
	 }
	 var shipping_type = '';
	 for(var i=0;i<data.length;i++){
	 text+= '<table width="800" border="0" cellpadding="0" cellspacing="0">';
	 text+= ' <tr>';
	 text+= '<td  colspan="3" heigh="30" align="center"><b>'+title+'</b></td>';
	 text+= '<td width="270" rowspan="11" valign="middle"><p>友</p>';
	 text+= '<p>件</p>';
	 text+= '<p>网</p></td>';
	 text+= '</tr>';
	 text+= '<tr>';
	 text+= '<td width="295" height="25" colspan="1">货号：'+data[i]['goods_sn']+'</td>';
	 text+= '<td width="325" height="25" align="left" colspan="1">'+formatDateTime(data[i]['add_time'])+'</td>';
	 text+= '<td width="205" height="25" align="center" height="5">票号：'+data[i]['logistics_sn']+'</td>';
	 text+= '</tr>';
	 text+= '<tr>';
	 text+= '<td colspan="3">';
	 text+='<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
	 text+= '<tr bgcolor="#000000">';
	 text+= '<td width="15%" height="5" align="center" bgcolor="#FFFFFF">'+Name_1+'</td>';
	 text+= '<td width="53%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['receiving_name'],13);
	 text+='</td>';
	 text+= '<td width="12%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+= '<td width="5%" height="5" colspan="3" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_phone'],11)+'</td>';
	 text+= '</tr>';
	 text+= '<tr  bgcolor="#000000">';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+Name_2+'</td>';
	 text+= '<td height="5" align="center" colspan="2"  bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['member_name'],13);
	 text+='</td>';
	 text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+= '<td height="5" colspan="3" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['member_phone'],11)+'</td>';
	 text+= '</tr>';
	 text+= '<tr  bgcolor="#000000">';
	 text+= '<td height="5" align="center"rowspan="2" bgcolor="#FFFFFF">地址</td>';
	 text+= '<td colspan="5" rowspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_name_area'],20)+'</td>';
	 text+= '<td width="28%" height="5" align="center" bgcolor="#FFFFFF">保价金</td>';
	 text+= '<td width="13%" height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	 text+= '</tr>';
	 text+= '<tr  bgcolor="#000000">';	
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">保价费</td>';
    text+= '<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
    text+= '</tr>';
	

	text+='<tr  bgcolor="#000000">';
    text+='<td height="5"   align="center" bgcolor="#FFFFFF">'+Name_3+'</td>';
	text+='<td height="5"   align="center" bgcolor="#FFFFFF">'+data[i]['goods_price']+'</td>';
	text+='<td height="5" colspan="4" align="center" bgcolor="#FFFFFF">';
//	text+='<table width="90%" border="0" cellspacing="0" cellpadding="0">';
//    text+='<tr>';
//    text+='<td align="center">运费</td>';
//    text+='<td align="left">'+data[i]['freight']+'</td>';
//    text+='<td align="left">制单费</td>';// style="border-left:1px solid #000;"
//	text+='<td align="left">'+data[i]['make_from_price']+'</td>';
//    text+='</tr>';
//    text+='</table>';
	text+='运费  ' + (parseFloat(data[i]['freight'])+parseFloat(data[i]['make_from_price']));
    text+='&nbsp;&nbsp;&nbsp;合计:'+(parseFloat(data[i]['freight'])+parseFloat(data[i]['goods_price']))+'</td>';
	switch(data[i]['shipping_type']){
	   case '1':
	       shipping_type = '提付';
	   break;
	   case '2':
		   shipping_type = '回付';
	   break;
	   case '3':
		   shipping_type = '已付';
	   break;
	 }
	text+= '<td height="5" align="center" colspan="3" bgcolor="#FFFFFF">'+shipping_type+'</td>';
	text+= '</tr>';
 var collection = '';
	switch(data[i]['collection']){
	  case '1':
       collection = '代收';
	  break;
	  case '2':
	   collection = '不代收';
	  break;
	}
	 //判断逻辑
	 var totalprice = 0;
	 if(collection == '代收'){
		  totalprice+=parseFloat(data[i]['goods_price']);
	 }
	 if(shipping_type=='提付'){
	       totalprice+=parseFloat(data[i]['freight'])+parseFloat(data[i]['make_from_price']);
	 }
	  text+= '<tr  bgcolor="#000000">';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">网址</td>';
	  var phone = '';
	  /*if(data[i]['same_city'] == 1){
          phone = '同城电话:';	   
	  }
	  else{
	      phone = '外埠电话:';
	  }*/
	  text+= '<td height="5" colspan="5" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;&nbsp;http://wuliu.youjian8.com&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+phone+'</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">件数</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['goods_num']+'</td>';
	  text+= '</tr>';
	
	/*text+= '<tr  bgcolor="#000000">';
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	text+= '<td height="5"  align="center"  bgcolor="#FFFFFF">&nbsp;</td>';
	text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	text+= '<td height="5" colspan="3" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">';

    var collection = '';
	switch(data[i]['collection']){
	  case '1':
       collection = '代收';
	  break;
	  case '2':
	   collection = '不代收';
	  break;
	}
	text+= collection;
	text+= '</td>';
	text+= '</tr>';*/

   
	text+= '<tr  bgcolor="#000000">';
	text+= '<td  colspan="3" rowspan="2"  bgcolor="#FFF"><div style="word-break:break-all;height:90px; overflow:hidden;background:#FFF">';
    text+= data[i]['routeInfo']['logistics_route_name'];
	text+= '</div></td>';
	text+= '<td height="50" width="11%" align="center" bgcolor="#FFFFFF">备注</td>';
	text+= '<td height="50"  colspan="4" align="center" bgcolor="#FFFFFF" style="font-size:11px;">'+TextSubStr(data[i]['remark'],50)+'</td>';
	text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	text+= '<td height="5"  align="center" bgcolor="#FFFFFF">小号</td>';
	var small_num = '';
	if(data[i]['small_num']!=null){
	 small_num = data[i]['small_num'];
	}
	text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+small_num+'</td>';
	text+= '<td height="5" colspan="2" bgcolor="#FFFFFF">操作员:'+data[i]['employee_name']+'</td>';
	//text+= '<td height="5" bgcolor="#FFFFFF">'+data[i]['employee_name']+'</td>';
	text+= '</tr>';
	text+= '</table>';
	text+= '</td>';
	text+= '</tr>';
	text+= '</table>';
	}
	  return text;
	} 

	/**
	* 开单发货样式
	**/
	function KD_Style_1(data){
		 var shipping_type = '';
		 var text   = '';
	    for(var i=0;i<data.length;i++){
			text+= '<table width="700" border="0" cellpadding="0" cellspacing="0" >';
		    text+= '<tr>';
	        text+= '<td  heigh="30" align="center"><b>友件物流收货单</b></td>';
			text+= '</tr>';
			text+= '<tr bgcolor="#000000">';
			text+= '<td  colspan="4" height="50"  align="left" bgcolor="#FFFFFF">打印时间:'+formatDateTime(data[i]['add_time'])+'</td>';
			text+= '<td  colspan="3" height="50"  align="left" bgcolor="#FFFFFF">票号：'+data[i]['logistics_sn']+'</td>';
			text+= '</tr>';
			text+= '<tr bgcolor="#000000">';
			text+= '<td width="38%" height="30" colspan="3" align="left" bgcolor="#FFFFFF">收件人:'+TextSubStr(data[i]['receiving_name'],13)+'</td>';
			text+= '<td width="25%" height="30" colspan="3" align="left" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_phone'],11)+'</td>';
			text+= '<td width="30%" height="30" align="left" bgcolor="#FFFFFF">到站:'+data[i]['routeInfo']['logistics_route_name']+'</td>';
			text+= '</tr>';
			text+= '<tr  bgcolor="#000000">';
			text+= '<td height="30" align="left" colspan="3"  bgcolor="#FFFFFF">发货人:'+TextSubStr(data[i]['member_name'],13)+'</td>';
			text+= '<td height="30" colspan="3" align="left" bgcolor="#FFFFFF">'+TextSubStr(data[i]['member_phone'],11)+'</td>';
			var typeNmae = '';
			switch(data[i]['order_type']){
				case '1':
				typeNmae = '通达';
				break;
				case '3':
				typeNmae = '宣化';
				break;
			}
			text+= '<td width="28%" height="30" colspan="2" align="left" bgcolor="#FFFFFF">类型:'+typeNmae+'</td>';
			text+= '</tr>';

			text+= '<tr  bgcolor="#000000">';
			text+= '<td height="30" colspan="6" rowspan="3" align="left" valign="top" style="padding-top:10px"  bgcolor="#FFFFFF">货物名称:'+TextSubStr(data[i]['remark'],50)+' </td>';
			text+= '<td height="30" align="left" bgcolor="#FFFFFF">件数:'+data[i]['goods_num']+'</td>';
			text+= '</tr>';

			text+= '<tr  bgcolor="#000000">';
			switch(data[i]['shipping_type']){
			   case '1':
				   shipping_type = '提付';
			   break;
			   case '2':
				   shipping_type = '回付';
			   break;
			   case '3':
				   shipping_type = '已付';
			   break;
			 }
			text+= '<td  height="30" align="left" bgcolor="#FFFFFF">'+shipping_type+':'+(parseFloat(data[i]['freight'])+parseFloat(data[i]['make_from_price']))+'</td>';
			text+= '</tr>';

			 var collection = '';
			switch(data[i]['collection']){
			  case '1':
			   collection = '代收';
			  break;
			  case '2':
			   collection = '不代收';
			  break;
			}
			 //判断逻辑
			 var totalprice = 0;
			 if(collection == '代收'){
				  totalprice+=parseFloat(data[i]['goods_price']);
			 }
			 if(shipping_type=='提付'){
				   totalprice+=parseFloat(data[i]['freight'])+parseFloat(data[i]['make_from_price']);
			 }

			text+= '<tr  bgcolor="#000000">';
			text+= '<td height="30"  colspan="2" align="left" bgcolor="#FFFFFF">'+collection+':'+parseFloat(data[i]['goods_price'])+'</td>';
			text+= '</tr>';

			text+= '<tr  bgcolor="#000000">';
			text+= '<td height="30" colspan="6"  align="left" bgcolor="#FFFFFF">'+DX(totalprice)+'</td>';
			text+= '<td  height="30" align="left" bgcolor="#FFFFFF">合计:'+totalprice+'</td>';
			text+= '</tr>';

			text+= '<tr  bgcolor="#000000">';
			text+= '<td  colspan="6"  bgcolor="#FFF" height="90">';
			text+= '<div style="word-break:break-all;height:30px; overflow:hidden;background:#FFF">备注:<span style="font-size:11px;">'+data[i]['remark']+'</span></div></td>';
			text+= '<td  height="30" align="left" bgcolor="#FFFFFF">操作员:'+data[i]['employee_name']+'</td>';
			text+= '</tr>';
			text+= '</table>';
		}
		return text;
	}
	  /**
     * 时间戳转换
     * @param timeStamp
     * @returns {String}
     * 靳健
     */
    function formatDateTime(timeStamp) {   
        var date = new Date();  
        date.setTime(timeStamp * 1000);  
        var y = date.getFullYear();      
        var m = date.getMonth() + 1;      
        m = m < 10 ? ('0' + m) : m;      
        var d = date.getDate();      
        d = d < 10 ? ('0' + d) : d;      
        var h = date.getHours();    
        h = h < 10 ? ('0' + h) : h;    
        var minute = date.getMinutes();    
        var second = date.getSeconds();    
        minute = minute < 10 ? ('0' + minute) : minute;      
        second = second < 10 ? ('0' + second) : second;     
        return y + '-' + m + '-' + d +' '+h+':'+minute+':'+second;      
    };
    /**
     * 合计大写转换
     * 靳健
     */
    function DX(n) {
		if (n==0)
		    return '零元';
        if (!/^(0|[1-9]\d*)(\.\d+)?$/.test(n))
            return "请填写金额";
        var unit = "千百拾亿千百拾万千百拾元角分", str = "";
            n += "00";
        var p = n.indexOf('.');
        if (p >= 0)
            n = n.substring(0, p) + n.substr(p+1, 2);
            unit = unit.substr(unit.length - n.length);
        for (var i=0; i < n.length; i++)
            str += '零壹贰叁肆伍陆柒捌玖'.charAt(n.charAt(i)) + unit.charAt(i);
        return str.replace(/零(千|百|拾|角)/g, "零").replace(/(零)+/g, "零").replace(/零(万|亿|元)/g, "$1").replace(/(亿)万|壹(拾)/g, "$1$2").replace(/^元零?|零分/g, "").replace(/元$/g, "元整");
}
/*格式化物流单号
*  no 物流单号
*  type 1为条形识别码单号;2位货物件数
*/
function FormatLogisticsSn(no,type){
   var new_sn = no.split('_');
   if(type==1)
   {
	 var new_sn_1 = new_sn[1].split('/');
	 return new_sn[0]+'_'+new_sn_1[1];
   }
   else if(type==2)
   {
     return new_sn[1];
   }
}
function pad(num, n) {  
    var len = num.toString().length;  
    while(len < n) {  
        num = "0" + num;  
        len++;  
    }  
    return num;  
}  
function TextSubStr(data,leng){
    var txt = '';
	data = data.replace(/\s+/g,"");
	if(data.length>leng){
	  txt = '...';
	}
    return data.substr(0,leng)+txt;
}