/**
* 打印功能
* 王子强
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
    //打印货签
	//type 1为退货2为发货
    function print_goods_tag(data,type){
    var type = arguments[1] ? arguments[1] : 2;//设置参数a的默认值为1 
	//	 LODOP=getLodop();
    //JSON.parse(str)
	 if(data==''){return;}
	 LODOP.PRINT_INIT("打印货签");
	// console.log(type);
	// return;
	for(var i=0;i<data[0]['goodsInfo'].length;i++){
	   LODOP.NewPage();
	   //===============================================================
	   LODOP.SET_PRINT_PAGESIZE(1,900,600,"CreateCustomPage");
	   //===============================================================
	   LODOP.ADD_PRINT_HTM(85,25,'100mm','100mm',GoodsTag_Style(data,i,type));//70
	}
 	 LODOP.SET_PRINTER_INDEX('WL_HUOTIE');  //打印机名称需提前统一并且设置好WL_HUOTIE
	 //LODOP.SET_PRINT_MODE("CATCH_PRINT_STATUS",true);

			/*LODOP.On_Return=function(TaskID,Value){
				console.log(LODOP.GET_VALUE("PRINT_STATUS_OK",Value));
				if (LODOP.GET_VALUE("PRINT_STATUS_OK",Value)) {
				
					  console.log('打印成功');
				
               }
	      	}*/
		//console.log(data);
		// LODOP.PREVIEW();
		 LODOP.PRINT();

	}
	//开单时打印
	 function printKD(data) {
		 LODOP.PRINT_INIT("打印开单存根");
		 LODOP.SET_PRINT_STYLE("FontSize",8);
		 LODOP.SET_PRINT_PAGESIZE(2,297,0,'CreateCustomPage');
		 LODOP.ADD_PRINT_HTM(0,0,'72mm','297mm',KD_Style(data));
		 LODOP.SET_PRINTER_INDEX('WL_CUNGEN'); //打印机名称需提前统一并且设置好 
	    // LODOP.PREVIEW();
	     LODOP.PRINT(); 
    }
	// 打印存根
	//type 1为退货2为发货
    function printCounterfoil(data,type) {
    	 var type = arguments[1] ? arguments[1] : 2;
		 LODOP.PRINT_INIT("打印存根");
		 LODOP.SET_PRINT_STYLE("FontSize",8);
		 LODOP.SET_PRINT_PAGESIZE(2,297,0,'CreateCustomPage');
		 LODOP.ADD_PRINT_HTM(0,0,'72mm','297mm',Counterfoil_Style(data,type));
		 LODOP.SET_PRINTER_INDEX('WL_CUNGEN'); //打印机名称需提前统一并且设置好 
	    // LODOP.PREVIEW();
	     LODOP.PRINT(); 
    }
	
		//type 1为退货2为发货
    //单独打印正
    function printCounterfoil_Z(data,type) {
    	 var type = arguments[1] ? arguments[1] : 2;
		 LODOP.PRINT_INIT("打印存根");
		 LODOP.SET_PRINT_STYLE("FontSize",8);
		 LODOP.SET_PRINT_PAGESIZE(2,297,0,'CreateCustomPage');
		 LODOP.ADD_PRINT_HTM(0,0,'72mm','297mm',Counterfoil_Style_Z(data,type));
		 LODOP.SET_PRINTER_INDEX('WL_CUNGEN'); //打印机名称需提前统一并且设置好 
	    // LODOP.PREVIEW();
	     LODOP.PRINT(); 
    }
    //打印票据
   function printreceipt(data) {
		 LODOP.PRINT_INIT("打印票据");
		 LODOP.SET_PRINT_STYLE("FontSize",8);
		 LODOP.SET_PRINT_PAGESIZE(1,300,0,'CreateCustomPage');

		 LODOP.ADD_PRINT_HTM(0,0,"RightMargin:0mm","BottomMargin:0mm",Receipt_Style(data));
		 LODOP.SET_PRINTER_INDEX('WL_PIAOJU'); //打印机名称需提前统一并且设置好 
	    // LODOP.PREVIEW();
	    LODOP.PRINT(); 
    }
   // 司机打印小码单
   function printSmallReceipt(data) {
       LODOP.PRINT_INIT("小码单打印");
       LODOP.SET_PRINT_COPIES(1);
       LODOP.SET_PRINT_PAGESIZE(0,'72mm','3267mm','小码单打印');
       LODOP.ADD_PRINT_HTM(0,0,"100%","100%",smallReceipt(data));
       LODOP.SET_PRINTER_INDEX('WL_CUNGEN'); //打印机名称需提前统一并且设置好 
      // console.log(smallReceipt(data));
     //  LODOP.PREVIEW();
      LODOP.PRINT(); 
   }
   //财务存根打印
   function printfinancial(data) {
		 LODOP.PRINT_INIT("财务存根打印");
		 LODOP.SET_PRINT_STYLE("FontSize",8);
		 LODOP.SET_PRINT_PAGESIZE(2,0,0,'CreateCustomPage');
		 LODOP.ADD_PRINT_HTM(0,0,"RightMargin:0mm","BottomMargin:0mm",financial(data));
		 LODOP.SET_PRINTER_INDEX('WL_CUNGEN'); //打印机名称需提前统一并且设置好 
	    // console.log(financial(data));
		//LODOP.PREVIEW();
	    LODOP.PRINT(); 
    }
   //财务存根打印2
   function printfinancial_T(data) {
		 LODOP.PRINT_INIT("财务存根打印2");
		 LODOP.SET_PRINT_STYLE("FontSize",8);
		 LODOP.SET_PRINT_PAGESIZE(2,0,0,'财务存根打印2');
		 LODOP.ADD_PRINT_HTM(0,0,"RightMargin:0mm","BottomMargin:0mm",financial_T(data));
		 LODOP.SET_PRINTER_INDEX('WL_CUNGEN'); //打印机名称需提前统一并且设置好 
	    // console.log(financial(data));
		//LODOP.PREVIEW();
	    LODOP.PRINT(); 
    }
	//===========================
     //货签函数
     function GoodsTag_Style(data,i,type){
	 //console.log(data);
 	  var text = '';	
	  var code = pad(data[0]['goodsInfo'][i]['goods_sn'],25);
	     //如果最后数量为双位数，长度最长25，否则可以到29
          LODOP.ADD_PRINT_BARCODE(10,10,293,50,"128Auto",code);
		  LODOP.SET_PRINT_STYLEA(0,"ShowBarText",0);
		  LODOP.ADD_PRINT_TEXT(62,45,293,20,FormatLogisticsSn(data[0]['goodsInfo'][i]['goods_sn'],1));
		  LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
          text+='<table width="100%"   border="0" cellspacing="0" cellpadding="0">';
		  text+='<tr>';
		  text+='<td colspan="2">';
          text+='<table width="70%" border="0" cellspacing="0" cellpadding="0">';
		  text+='<tr>';
		  var OrderType = '';
			   switch(data[0]['order_type']){
			      case '1':
					 OrderType = '西部';
				  break;
				  case '3':
					 OrderType = '瑞胜';
				  break;
				  case '4':
					 OrderType = '塔湾';
				  break;
			   }
		
          if(data[0]['same_city'] == 1){
			  if(type==2){
  			   text+='<td width="50%" align="center"><h3 style="margin:0px;padding:0px">'+OrderType+'&nbsp;=>&nbsp;'+data[0]['routeInfo']['logistics_route_name']+'</h3></td>';
			  }
			  else if(type==1)
			  {			 
			   text+='<td width="50%" align="center"><h3 style="margin:0px;padding:0px">'+data[0]['from_city']+'&nbsp;=>&nbsp;'+OrderType+'</h3></td>';
			  }
		  }
		  else{
			  if(type==2)
			  {
			   text+='<td width="50%" align="center"><h3 style="margin:0px;padding:0px">'+OrderType+'=>'+data[0]['city']+data[0]['district']+'</h3></td>';
		      }
			  else if(type==1)//退货
			  {			   
		       text+='<td width="50%" align="center"><h3 style="margin:0px;padding:0px">'+data[0]['from_city']+'&nbsp;=>&nbsp;'+OrderType+'</h3></td>';
			  }
		  }
		  text+='<td width="10%"><h2 style="margin:0px;padding:0px">'+FormatLogisticsSn(data[0]['goodsInfo'][i]['goods_sn'],2)+'</h2></td>';
		  if(type==1){
		   text+='<td width="10%"><h2 style="margin:0px;padding:0px">退</h2></td>';
		  }
	      text+='</tr>';
	      text+='</table>';
		  text+='</td>';
		  text+='</tr>';
		  text+='<tr>';
		 var shipping_type ='';
			switch(data[0]['shipping_type']){
		    case '1':
			   shipping_type = '提付';
		    break;
		    //case '2':
			//   shipping_type = '回付';
		   // break;
		    case '3':
			   shipping_type = '已付';
		    break;
		  }
		  text+='<td height="25" width="20%">物流单号</td>';
		  text+='<td height="25" width="80%"  align="left">'+data[0]['logistics_sn']+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td height="15" width="20%">运费</td>';
		  var price = parseFloat(data[0]['freight'])+parseFloat(data[0]['make_from_price']);
		  text+='<td height="15" width="80%"  align="left">'+price+'(<b>'+shipping_type+'</b>)&nbsp;&nbsp;</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td height="15" width="20%">收货人</td>';
		  text+='<td height="15" width="80%"  align="left">';
		  text+=TextSubStr(data[0]['receiving_name'],8);
		  text+='</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td height="15" width="20%">电话</td>';
		  text+='<td height="15" width="80%"  align="left">'+data[0]['receiving_phone']+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td height="20" width="20%">生成日期</td>';
		  text+='<td height="20" width="80%"  align="left">'+formatDateTime(data[0]['add_time'])+'</td>';
		  text+='</tr>';
		  text+='</table>';
		 return text;

	}
    //存根函数
	function Counterfoil_Style(data,type){
	 var text   = '';
	 var _title = '';
	 var title = '友件网物流货运送货单(正)';
	 var shipping_type = '';
	 for(var i=0;i<data.length;i++){
	 text+= '<table width="770" border="0" cellpadding="0" cellspacing="0">';
	 text+= ' <tr>';
	 if(type==1){
	   if(data[i]['return_type'] == 1) {
	       title = '友件网物流货运返货单(正)';
	   }else if(data[i]['return_type'] == 2) {
	       title = '友件网物流货运退货单(正)';
	   }
	 }
	 text+= '<td  colspan="3" align="center"><b>'+title+'</b></td>';
	 text+= '<td width="270" rowspan="11" valign="middle"><p>友</p>';
	 text+= '<p>件</p>';
	 text+= '<p>网</p></td>';
	 text+= '</tr>';
	 if(type==1){
	 text+= '<tr>';
	 text+= '<td width="380" height="30" colspan="1">货号：'+data[i]['goods_sn']+'</td>';
	 text+= '<td width="190" height="30" align="left" colspan="1">'+formatDateTime(data[i]['add_time'])+'</td>';
	 text+= '<td width="190" height="30" colspan="1" align="center" height="5">票号：'+data[i]['logistics_sn']+'</td>';
	 text+= '</tr>';
	 }
	 else{
	 text+= '<tr>';
	 text+= '<td width="380" height="30" colspan="1">货号：'+data[i]['goods_sn']+'</td>';
	 text+= '<td width="190" height="30" align="left" colspan="1">'+formatDateTime(data[i]['add_time'])+'</td>';
	 text+= '<td width="190" height="30" colspan="1" align="center" height="5">票号：'+data[i]['logistics_sn']+'</td>';
	 text+= '</tr>';
	 }
	 text+= '<tr>';
	 text+= '<td colspan="4">';
	 text+='<table width="89%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
	 text+= '<tr bgcolor="#000000">';
	 text+= '<td width="15%" height="5" align="center" bgcolor="#FFFFFF">收货人</td>';
	 text+= '<td width="19%" height="5" align="center" bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['receiving_name'],8);
	 text+='</td>';
	 text+= '<td width="7%" height="5" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+= '<td width="15%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_phone'],11)+'</td>';
	 text+= '<td width="8%" height="5" align="center" bgcolor="#FFFFFF">货名</td>';
	 text+= '<td width="8%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	 text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">发货人</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['member_name'],8);
	 text+='</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['member_phone'],11)+'</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">件数</td>';
	 text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+data[i]['goods_num']+'</td>';
	 text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	 text+= '<td height="5" align="center"rowspan="2" bgcolor="#FFFFFF">地址</td>';
	 text+= '<td colspan="5" rowspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_name_area'],20)+'</td>';
	 text+= '<td width="12%" height="5" align="center" bgcolor="#FFFFFF">保价金额</td>';
	 text+= '<td width="10%" height="5" align="center" bgcolor="#FFFFFF">保价费</td>';
	 text+= '</tr>';
	 text+= '<tr  bgcolor="#000000">';	
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
    text+= '<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
    text+= '</tr>';
	 if(type==1){
	text+= '<tr  bgcolor="#000000">';
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">退货货值</td>';
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
	text+='</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['goods_price']+'</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">运费</td>';
	//text+= '<td height="5" align="center"  bgcolor="#FFFFFF">'+data[i]['freight']+'</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">制单费</td>';
	//text+= '<td height="5" align="center" colspan="2" bgcolor="#FFFFFF">'+data[i]['make_from_price']+'</td>';
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
	}
	if(type==2){
	text+='<tr  bgcolor="#000000">';
    text+='<td height="5"   align="center" bgcolor="#FFFFFF">代收款</td>';
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
    text+='</td>';
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
	  var collection = '';
	switch(data[i]['collection']){
	  case '1':
       collection = '代收';
	  break;
	  case '2':
	   collection = '不代收';
	  break;
	}
	text+= '<td height="5" align="center" colspan="3" bgcolor="#FFFFFF">'+shipping_type+'&nbsp;&nbsp;'+collection+'</td>';
	text+= '</tr>';
	}


     if(type==2){
	 //判断逻辑
	 var totalprice = 0;
	 if(collection == '代收'){
		  totalprice+=parseFloat(data[i]['goods_price']);
	 }
	 if(shipping_type=='提付'){
	       totalprice+=parseFloat(data[i]['freight'])+parseFloat(data[i]['make_from_price']);
	 }
	  text+= '<tr  bgcolor="#000000">';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计大写</td>';
	  text+= '<td height="5" colspan="5" align="center" bgcolor="#FFFFFF">'+DX(data[i]['all_amount'])+'</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['all_amount']+'</td>';
	  text+= '</tr>';
	}
	else if(type==1){
	  text+= '<tr  bgcolor="#000000">';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计大写</td>';
	  text+= '<td height="5" colspan="5" align="center" bgcolor="#FFFFFF">'+DX(data[i]['all_amount'])+'</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['all_amount']+'</td>';
	  text+= '</tr>';
	} 

  text+= '<tr  bgcolor="#000000" >';
	text+='<td height="5"   align="center" bgcolor="#FFFFFF">订单编号</td>';
	text+= '<td  colspan="3"  bgcolor="#FFF"><div style="word-break:break-all;height:40px;font-size:11px; overflow:hidden;background:#FFF">';
	if(data[i]['order_sn'] != null){
	  if(data[i]['order_sn'].indexOf('"')>0){
	     var sn = data[i]['order_sn'].split('"');
		 var sn = sn[1].split(',');
		 for(var j=0;j<sn.length;j++){
		   if(j%2==0 && j!=0){
		    text+= '<br>';
		   }
		   text+=sn[j]+',';
		 }
		}
	  else{
		 var sn = data[i]['order_sn'].split(',');
		 for(var j=0;j<sn.length;j++){
		   if(j%2==0 && j!=0){
		    text+= '<br>';
		   }
		   text+=sn[j]+',';
		 }
	  }
	}
    text+= '</div>';
	text+= '</td>';
	text+= '<td height="60" width="11%" align="center" bgcolor="#FFFFFF">备注</td>';
	text+= '<td height="60"  colspan="4" align="center" bgcolor="#FFFFFF" style="font-size:11px;">'+TextSubStr(data[i]['remark'],50)+'</td>';
	text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	if(type==2){
	 text+='<td height="5"  align="center" bgcolor="#FFFFFF">线路</td>';
	 text+='<td  colspan="3"  bgcolor="#FFF">'+data[i]['routeInfo']['logistics_route_name']+'</td>';
	}
	else{
	text+='<td height="5"  align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	text+='<td  colspan="3"  bgcolor="#FFF">&nbsp;</td>';
	}
	text+= '<td height="5"  align="center" bgcolor="#FFFFFF">小号</td>';
	var small_num = '';
	if(data[i]['small_num']!=null){
	 small_num = data[i]['small_num'];
	}
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+small_num+'</td>';
	text+= '<td height="5" colspan="2" bgcolor="#FFFFFF">操作员:&nbsp;</td>';
	//text+= '<td height="5" bgcolor="#FFFFFF">'+data[i]['employee_name']+'</td>';
	text+= '</tr>';
	text+= '</table>';
	text+= '</td>';
	text+= '</tr>';
	text+= '</table>';
	// }//上一层循环
	// var text = '';
	 var _title = '友件网物流货运送货单(副)';
	 var shipping_type = '';
	// for(var i=0;i<data.length;i++){
	 text+= '<table width="770" border="0" cellpadding="0" cellspacing="0">';
	 text+= ' <tr>';
	 if(type==1){
	     if(data[i]['return_type'] == 1) {
	        _title = '友件网物流货运返货单(副)';
	     } else if(data[i]['return_type'] == 2) {
	        _title = '友件网物流货运退货单(副)';
	     }
	 }
	 text+= '<td  colspan="3" align="center"><b>'+_title+'</b></td>';
	 text+= '<td width="270" rowspan="11" valign="middle"><p>*</p><p>友</p>';
	 text+= '<p>件</p>';
	 text+= '<p>网</p></td>';
	 text+= '</tr>';
	 if(type==1){
	 text+= '<tr>';
	 text+= '<td width="380" height="30" colspan="1">货号：'+data[i]['goods_sn']+'</td>';
	 text+= '<td width="190" height="30" align="left" colspan="1">'+formatDateTime(data[i]['add_time'])+'</td>';
	 text+= '<td width="190" height="30" colspan="1" align="center" height="5">票号：'+data[i]['logistics_sn']+'</td>';
	 text+= '</tr>';
	 }
	 else{
	 text+= '<tr>';
	 text+= '<td width="380" height="30" colspan="1">货号：'+data[i]['goods_sn']+'</td>';
	 text+= '<td width="190" height="30" align="left" colspan="1">'+formatDateTime(data[i]['add_time'])+'</td>';
	 text+= '<td width="190" height="30" colspan="1" align="center" height="5">票号：'+data[i]['logistics_sn']+'</td>';
	 text+= '</tr>';
	 }
	 text+= '<tr>';
	 text+= '<td colspan="4">';
	 text+='<table width="89%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
	 text+= '<tr bgcolor="#000000">';
	 text+= '<td width="15%" height="5" align="center" bgcolor="#FFFFFF">收货人</td>';
	 text+= '<td width="19%" height="5" align="center" bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['receiving_name'],8);
	 text+='</td>';
	 text+= '<td width="7%" height="5" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+= '<td width="15%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_phone'],11)+'</td>';
	 text+= '<td width="8%" height="5" align="center" bgcolor="#FFFFFF">货名</td>';
	 text+= '<td width="8%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	 text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">发货人</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['member_name'],8);
	 text+='</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['member_phone'],11)+'</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">件数</td>';
	 text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+data[i]['goods_num']+'</td>';
	 text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	 text+= '<td height="5" align="center"rowspan="2" bgcolor="#FFFFFF">地址</td>';
	 text+= '<td colspan="5" rowspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_name_area'],20)+'</td>';
	 text+= '<td width="12%" height="5" align="center" bgcolor="#FFFFFF">保价金额</td>';
	 text+= '<td width="10%" height="5" align="center" bgcolor="#FFFFFF">保价费</td>';
	 text+= '</tr>';
	 text+= '<tr  bgcolor="#000000">';	
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
    text+= '<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
    text+= '</tr>';
	 if(type==1){
	text+= '<tr  bgcolor="#000000">';
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">退货货值</td>';
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
    text+='</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['goods_price']+'</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">运费</td>';
	//text+= '<td height="5" align="center"  bgcolor="#FFFFFF">'+data[i]['freight']+'</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">制单费</td>';
	//text+= '<td height="5" align="center" colspan="2" bgcolor="#FFFFFF">'+data[i]['make_from_price']+'</td>';
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
	}
	if(type==2){
	text+='<tr  bgcolor="#000000">';
    text+='<td height="5"   align="center" bgcolor="#FFFFFF">代收款</td>';
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
    text+='</td>';
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
    var collection = '';
	switch(data[i]['collection']){
	  case '1':
       collection = '代收';
	  break;
	  case '2':
	   collection = '不代收';
	  break;
	}
	text+= '<td height="5" align="center" colspan="3" bgcolor="#FFFFFF">'+shipping_type+'&nbsp;&nbsp;'+collection+'</td>';
	text+= '</tr>';
	}


     if(type==2){
	 //判断逻辑
	 var totalprice = 0;
	 if(collection == '代收'){
		  totalprice+=parseFloat(data[i]['goods_price']);
	 }
	 if(shipping_type=='提付'){
	       totalprice+=parseFloat(data[i]['freight'])+parseFloat(data[i]['make_from_price']);
	 }
	  text+= '<tr  bgcolor="#000000">';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计大写</td>';
	  text+= '<td height="5" colspan="5" align="center" bgcolor="#FFFFFF">'+DX(data[i]['all_amount'])+'</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['all_amount']+'</td>';
	  text+= '</tr>';
	}
	else if(type==1){
	  text+= '<tr  bgcolor="#000000">';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计大写</td>';
	  text+= '<td height="5" colspan="5" align="center" bgcolor="#FFFFFF">'+DX(data[i]['all_amount'])+'</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['all_amount']+'</td>';
	  text+= '</tr>';
	}   

  text+= '<tr  bgcolor="#000000">';
	text+='<td height="5"   align="center" bgcolor="#FFFFFF">订单编号</td>';
	text+= '<td  colspan="3"  bgcolor="#FFF"><div style="word-break:break-all;height:60px;font-size:11px; overflow:hidden;background:#FFF">';
	if(data[i]['order_sn'] != null){
	  if(data[i]['order_sn'].indexOf('"')>0){
	     var sn = data[i]['order_sn'].split('"');
		 var sn = sn[1].split(',');
		 for(var j=0;j<sn.length;j++){
		   if(j%2==0 && j!=0){
		    text+= '<br>';
		   }
		   text+=sn[j]+',';
		 }
		}
	  else{
		 var sn = data[i]['order_sn'].split(',');
		 for(var j=0;j<sn.length;j++){
		   if(j%2==0 && j!=0){
		    text+= '<br>';
		   }
		   text+=sn[j]+',';
		 }
	  }
	}
    text+= '</div>';
	text+= '</td>';
	text+= '<td height="60" width="11%" align="center" bgcolor="#FFFFFF">备注</td>';
	text+= '<td height="60"  colspan="4" align="center" bgcolor="#FFFFFF" style="font-size:11px;">'+TextSubStr(data[i]['remark'],50)+'</td>';
	text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	if(type==2){
	text+='<td height="5"  align="center" bgcolor="#FFFFFF">线路</td>';
	text+='<td  colspan="3"  bgcolor="#FFF">'+data[i]['routeInfo']['logistics_route_name']+'</td>';
	}
	else{
	text+='<td height="5"  align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	text+='<td  colspan="3"  bgcolor="#FFF">&nbsp;</td>';
	}
	text+= '<td height="5"  align="center" bgcolor="#FFFFFF">小号</td>';
	var small_num = '';
	if(data[i]['small_num']!=null){
	 small_num = data[i]['small_num'];
	}
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+small_num+'</td>';
	text+= '<td height="5" colspan="2" bgcolor="#FFFFFF">操作员:&nbsp;</td>';
	//text+= '<td height="5" bgcolor="#FFFFFF">'+data[i]['employee_name']+'</td>';
	text+= '</tr>';
	text+= '</table>';
	text+= '</td>';
	text+= '</tr>';
	text+= '</table>';
	 }
	  return text;
	} 
	 //存根函数
	function Counterfoil_Style_Z(data,type){
	 var text   = '';
	 var _title = '';
	 var title = '友件网物流货运送货单(正)';
	 var shipping_type = '';
	 for(var i=0;i<data.length;i++){
	 text+= '<table width="770" border="0" cellpadding="0" cellspacing="0">';
	 text+= ' <tr>';
	 if(type==1){
	   if(data[i]['return_type'] == 1) {
	       title = '友件网物流货运返货单(正)';
	   }else if(data[i]['return_type'] == 2) {
	       title = '友件网物流货运退货单(正)';
	   }
	 }
	 text+= '<td  colspan="3" align="center"><b>'+title+'</b></td>';
	 text+= '<td width="270" rowspan="11" valign="middle"><p>友</p>';
	 text+= '<p>件</p>';
	 text+= '<p>网</p></td>';
	 text+= '</tr>';
	 if(type==1){
	 text+= '<tr>';
	 text+= '<td width="380" height="30" colspan="1">货号：'+data[i]['goods_sn']+'</td>';
	 text+= '<td width="190" height="30" align="left" colspan="1">'+formatDateTime(data[i]['add_time'])+'</td>';
	 text+= '<td width="190" height="30" colspan="1" align="center" height="5">票号：'+data[i]['logistics_sn']+'</td>';
	 text+= '</tr>';
	 }
	 else{
	 text+= '<tr>';
	 text+= '<td width="380" height="30" colspan="1">货号：'+data[i]['goods_sn']+'</td>';
	 text+= '<td width="190" height="30" align="left" colspan="1">'+formatDateTime(data[i]['add_time'])+'</td>';
	 text+= '<td width="190" height="30" colspan="1" align="center" height="5">票号：'+data[i]['logistics_sn']+'</td>';
	 text+= '</tr>';
	 }
	 text+= '<tr>';
	 text+= '<td colspan="4">';
	 text+='<table width="89%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
	 text+= '<tr bgcolor="#000000">';
	 text+= '<td width="12%" height="5" align="center" bgcolor="#FFFFFF">收货人</td>';
	 text+= '<td width="19%" height="5" align="center" bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['receiving_name'],8);
	 text+='</td>';
	 text+= '<td width="7%" height="5" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+= '<td width="15%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_phone'],11)+'</td>';
	 text+= '<td width="8%" height="5" align="center" bgcolor="#FFFFFF">货名</td>';
	 text+= '<td width="8%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	 text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">发货人</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['member_name'],8);
	 text+='</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['member_phone'],11)+'</td>';
	 text+= '<td height="5" align="center" bgcolor="#FFFFFF">件数</td>';
	 text+= '<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">'+data[i]['goods_num']+'</td>';
	 text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	 text+= '<td height="5" align="center"rowspan="2" bgcolor="#FFFFFF">地址</td>';
	 text+= '<td colspan="5" rowspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_name_area'],20)+'</td>';
	 text+= '<td width="12%" height="5" align="center" bgcolor="#FFFFFF">保价金额</td>';
	 text+= '<td width="10%" height="5" align="center" bgcolor="#FFFFFF">保价费</td>';
	 text+= '</tr>';
	 text+= '<tr  bgcolor="#000000">';	
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
    text+= '<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
    text+= '</tr>';
	 if(type==1){
	text+= '<tr  bgcolor="#000000">';
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">退货货值</td>';
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
	text+='</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['goods_price']+'</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">运费</td>';
	//text+= '<td height="5" align="center"  bgcolor="#FFFFFF">'+data[i]['freight']+'</td>';
	//text+= '<td height="5" align="center" bgcolor="#FFFFFF">制单费</td>';
	//text+= '<td height="5" align="center" colspan="2" bgcolor="#FFFFFF">'+data[i]['make_from_price']+'</td>';
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
	}
	if(type==2){
	text+='<tr  bgcolor="#000000">';
    text+='<td height="5"   align="center" bgcolor="#FFFFFF">代收款</td>';
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
    text+='</td>';
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
	  var collection = '';
	switch(data[i]['collection']){
	  case '1':
       collection = '代收';
	  break;
	  case '2':
	   collection = '不代收';
	  break;
	}
	text+= '<td height="5" align="center" colspan="3" bgcolor="#FFFFFF">'+shipping_type+'&nbsp;&nbsp;'+collection+'</td>';
	text+= '</tr>';
	}


     if(type==2){
	 //判断逻辑
	 var totalprice = 0;
	 if(collection == '代收'){
		  totalprice+=parseFloat(data[i]['goods_price']);
	 }
	 if(shipping_type=='提付'){
	       totalprice+=parseFloat(data[i]['freight'])+parseFloat(data[i]['make_from_price']);
	 }
	  text+= '<tr  bgcolor="#000000">';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计大写</td>';
	  text+= '<td height="5" colspan="5" align="center" bgcolor="#FFFFFF">'+DX(data[i]['all_amount'])+'</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['all_amount']+'</td>';
	  text+= '</tr>';
	}
	else if(type==1){
	  text+= '<tr  bgcolor="#000000">';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计大写</td>';
	  text+= '<td height="5" colspan="5" align="center" bgcolor="#FFFFFF">'+DX(data[i]['all_amount'])+'</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">合计</td>';
	  text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['all_amount']+'</td>';
	  text+= '</tr>';
	}
	//if(type==2){
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
	//}
   

    text+= '<tr  bgcolor="#000000">';
	text+='<td height="5"   align="center" bgcolor="#FFFFFF">订单编号</td>';
	text+= '<td  colspan="3"  bgcolor="#FFF"><div style="word-break:break-all;height:60px;font-size:11px; overflow:hidden;background:#FFF">';
	if(data[i]['order_sn'] != null){
	  if(data[i]['order_sn'].indexOf('"')>0){
	     var sn = data[i]['order_sn'].split('"');
		 var sn = sn[1].split(',');
		 for(var j=0;j<sn.length;j++){
		   if(j%2==0 && j!=0){
		    text+= '<br>';
		   }
		   text+=sn[j]+',';
		 }
		}
	  else{
		 var sn = data[i]['order_sn'].split(',');
		 for(var j=0;j<sn.length;j++){
		   if(j%2==0 && j!=0){
		    text+= '<br>';
		   }
		   text+=sn[j]+',';
		 }
	  }
	}
    text+= '</div>';
	text+= '</td>';
	text+= '<td height="50" width="11%" align="center" bgcolor="#FFFFFF">备注</td>';
	text+= '<td height="50"  colspan="4" align="center" bgcolor="#FFFFFF" style="font-size:11px;">'+TextSubStr(data[i]['remark'],50)+'</td>';
	text+= '</tr>';
	text+= '<tr  bgcolor="#000000">';
	text+='<td height="5"  align="center" bgcolor="#FFFFFF">线路</td>';
	text+='<td  colspan="3"  bgcolor="#FFF">'+data[i]['routeInfo']['logistics_route_name']+'</td>';
	text+= '<td height="5"  align="center" bgcolor="#FFFFFF">小号</td>';
	var small_num = '';
	if(data[i]['small_num']!=null){
	 small_num = data[i]['small_num'];
	}
	text+= '<td height="5" align="center" bgcolor="#FFFFFF">'+small_num+'</td>';
	text+= '<td height="5" colspan="2" bgcolor="#FFFFFF">操作员:&nbsp;</td>';
	//text+= '<td height="5" bgcolor="#FFFFFF">'+data[i]['employee_name']+'</td>';
	text+= '</tr>';
	text+= '</table>';
	text+= '</td>';
	text+= '</tr>';
	text+= '</table>';
	// }//上一层循环
	// var text = '';	
	 }
	  return text;
	} 
	//开单存根
	function KD_Style(data){
	 var text   = '';
	 var _title = '';
	 var title = '友件网物流收货单';
	 var shipping_type = '';
	 for(var i=0;i<data.length;i++){
	 text+='<table width="800" border="0" cellpadding="0" cellspacing="0">';
	 text+='<tr>';
	 text+='<td  colspan="3" heigh="30" align="center"><b>'+title+'</b></td>';
	 text+='<td width="270" rowspan="11" valign="middle"><p>友</p>';
	 text+='<p>件</p>';
	 text+='<p>网</p></td>';
	 text+='</tr>';
	 text+='<tr>';
	 text+='<td width="295" height="25" colspan="1">货号：'+data[i]['goods_sn']+'</td>';
	 text+='<td width="155" height="25" align="left" colspan="1">'+formatDateTime(data[i]['add_time'])+'</td>';
	 text+='<td width="155" height="25" align="center" height="5">票号：'+data[i]['logistics_sn']+'</td>';
	 text+='</tr>';
	 text+='<tr>';
	 text+='<td colspan="3">';
	 text+='<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
	 text+='<tr bgcolor="#000000">';
	 text+='<td width="17%" height="5" align="center" bgcolor="#FFFFFF">收货人</td>';
	 text+='<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['receiving_name'],8);
	 text+='</td>';
	 text+='<td height="5" colspan="2" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+='<td height="5" colspan="3" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_phone'],18)+'</td>';
	 text+='</tr>';
	 text+='<tr  bgcolor="#000000">';
	 text+='<td height="5" align="center" bgcolor="#FFFFFF">发货人</td>';
	 text+='<td height="5" align="center" colspan="2"  bgcolor="#FFFFFF">';
	 text+=TextSubStr(data[i]['member_name'],8);
	 text+='</td>';
	 text+='<td height="5" width="15%" colspan="2" align="center" bgcolor="#FFFFFF">电话</td>';
	 text+='<td height="5" colspan="3" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['member_phone'],18)+'</td>';
	 text+='</tr>';
	 text+='<tr  bgcolor="#000000">';
	 text+='<td height="-1" align="center" bgcolor="#FFFFFF">地址</td>';
	 text+='<td colspan="2" align="center" bgcolor="#FFFFFF">'+TextSubStr(data[i]['receiving_name_area'],8)+'</td>';
	 text+='<td colspan="2" align="center" bgcolor="#FFFFFF">保价费</td>';
	 text+='<td align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	 text+='<td width="12%" height="5" align="center" bgcolor="#FFFFFF">保价金</td>';
	 text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
	 text+='</tr>';
	  text+='<tr  bgcolor="#000000">';
	  text+='<td height="5"   align="center" bgcolor="#FFFFFF">代收款</td>';
	  text+='<td width="22%" height="5"   align="center" bgcolor="#FFFFFF">'+data[i]['goods_price']+'</td>';
	  text+='<td height="5" colspan="4" align="center" bgcolor="#FFFFFF">';
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
	 text+='运费  ' + (parseFloat(data[i]['freight'])+parseFloat(data[i]['make_from_price']));
	 text+='</td>';
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
	 text+='<td height="5" align="center" colspan="3" bgcolor="#FFFFFF">'+shipping_type+'</td>';
	 text+='</tr>';
	  var phone = '';
	  if(data[i]['same_city'] == 1){
		  if(data[i]['order_type']==4){
		    phone = '塔湾电话:31809071';
		  }
		  else{
            phone = '同城电话:31809063';	   
		  }
	  }
	  else{
	      phone = '外埠电话:67791490';
	  }
	  text+='<tr  bgcolor="#000000">';
	  text+='<td height="5" align="center" bgcolor="#FFFFFF">网址</td>';
	  text+='<td height="5" colspan="5" bgcolor="#FFFFFF" style="font-size:11px">&nbsp;&nbsp;&nbsp;&nbsp;http://wuliu.youjian8.com&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+phone+'</td>';
	  text+='<td height="5" align="center" bgcolor="#FFFFFF">件数</td>';
	  text+='<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['goods_num']+'</td>';
	  text+='</tr>';
	text+='<tr  bgcolor="#000000">';
	text+='<td height="5"   align="center" bgcolor="#FFFFFF">订单编号</td>';
	text+='<td  colspan="3"  bgcolor="#FFF"><div style="word-break:break-all;height:60px;font-size:11px; overflow:hidden;background:#FFF">';
if(data[i]['order_sn'] != null){
	  if(data[i]['order_sn'].indexOf('"')>0){
	     var sn = data[i]['order_sn'].split('"');
		 var sn = sn[1].split(',');
		 for(var j=0;j<sn.length;j++){
		   if(j%2==0 && j!=0){
		    text+= '<br>';
		   }
		   text+=sn[j]+',';
		 }
		}
	  else{
		 var sn = data[i]['order_sn'].split(',');
		 for(var j=0;j<sn.length;j++){
		   if(j%2==0 && j!=0){
		    text+= '<br>';
		   }
		   text+=sn[j]+',';
		 }
	  }
	}
    text+=' </div>';
	text+='</td>';
	text+='<td height="50" width="12%" align="center" bgcolor="#FFFFFF">备注</td>';
	text+='<td height="50"  colspan="4" align="center" bgcolor="#FFFFFF" style="font-size:11px;">'+TextSubStr(data[i]['remark'],50)+'</td>';
	text+='</tr>';
	text+='<tr  bgcolor="#000000">';
	text+='<td height="2"  align="center" bgcolor="#FFFFFF">线路</td>';
	text+='<td  colspan="3"  bgcolor="#FFF">'+data[i]['routeInfo']['logistics_route_name']+'</td>';
	text+='<td height="2"  align="center" bgcolor="#FFFFFF">小号</td>';
	var small_num = '';
	if(data[i]['small_num']!=null){
	 small_num = data[i]['small_num'];
	}
	text+='<td width="22%" height="2" align="center" bgcolor="#FFFFFF">'+small_num+'</td>';
	text+='<td height="2" colspan="2" bgcolor="#FFFFFF">操作员:'+data[i]['employee_name']+'</td>';
	text+='</tr>';
	text+='<tr  bgcolor="#000000">';
	text+='<td height="2"  align="center" bgcolor="#FFFFFF">发货须知</td>';
	text+='<td height="2" colspan="7"  align="center" bgcolor="#FFFFFF"><p style="font-size:11px"><strong>玻璃及玻璃制品破损、外包装完好内部物品破损友件物流不负责赔偿</strong></p></td>';
	text+='</tr>';
	text+='</table>';
	text+='</td>';
	text+='</tr>';
	text+='</table>';
	}
	  return text;
	} 
    //票据函数
	function Receipt_Style(data){
		var newDate = new Date();
		var text  = '';
		var type  = '';
		var total = 0;
		var goodsTotal = 0;
		var T_HP = 0;
	    var T_YP = 0;
	    var T_TP = 0;
		text+='<table width="765" border="0" cellspacing="0" cellpadding="0">';
		text+='<tr height="40">';
		text+='<td width="30%">&nbsp;</td>';
		text+='<td width="30%">站点</td>';
		text+='<td width="30%">日期：'+newDate.toLocaleDateString()+'</td>';
		text+='</tr>';
		text+='</table>';
		//
		text+='<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">';
        text+='<tr>';
        text+='<td>';
        //
		text+='<table width="100%"  border="0" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF">';
		text+='<tr>';
		text+='<td align="center" width="10%" bgcolor="#FFFFFF">货号</td>';
		text+='<td align="center" width="15%" bgcolor="#FFFFFF">票号</td>';
		text+='<td align="center" width="15%" bgcolor="#FFFFFF">收货人</td>';
		text+='<td align="center" width="15%" bgcolor="#FFFFFF">发货人</td>';
		text+='<td align="center" width="5%" bgcolor="#FFFFFF">件数</td>';
		text+='<td align="center" width="5%" bgcolor="#FFFFFF">已付</td>';
		text+='<td align="center" width="5%" bgcolor="#FFFFFF">提付</td>';
		text+='<td align="center" width="5%" bgcolor="#FFFFFF">回付</td>';
		//text+='<td align="center" width="5%" bgcolor="#FFFFFF">状态</td>';
		text+='<td align="center" width="10%" bgcolor="#FFFFFF">代货款</td>';
		text+='<td align="center" width="10%" bgcolor="#FFFFFF">合计</td>';
		text+='<td align="center" width="5%" bgcolor="#FFFFFF">备注</td>';
		text+='</tr>';
		data.forEach(function(value){
		text+='<tr>';
		text+='<td align="center" bgcolor="#FFFFFF">'+value['goods_sn']+'</td>';
		text+='<td align="center" bgcolor="#FFFFFF">'+value['logistics_sn']+'</td>';
		text+='<td align="center" bgcolor="#FFFFFF">'+value['receiving_name']+'</td>';
		text+='<td align="center" bgcolor="#FFFFFF">'+value['member_name']+'</td>';
		text+='<td align="center" bgcolor="#FFFFFF">'+value['goods_num']+'</td>';
	   var HP = 0;
	   var YP = 0;
	   var TP = 0;
	   switch(value['shipping_type']){
	   case '1':
	       TP = parseFloat(value['freight'])+parseFloat(value['make_from_price']);
	       T_TP+= parseFloat(TP);
	   break;
	   case '2':
	       HP = parseFloat(value['freight'])+parseFloat(value['make_from_price']);
	       T_HP+= parseFloat(HP);
	   break;
	   case '3':
	       YP = parseFloat(value['freight'])+parseFloat(value['make_from_price']);
	       T_YP+= parseFloat(YP);
	   break;
	   }
		text+='<td align="center" bgcolor="#FFFFFF">'+YP+'</td>';
		text+='<td align="center" bgcolor="#FFFFFF">'+TP+'</td>';
		text+='<td align="center" bgcolor="#FFFFFF">'+HP+'</td>';
	//	var DP = 0;
		//if(value['collection'] == 1){
		 //  DP = value['goods_price'];
		//}
		text+='<td align="center" bgcolor="#FFFFFF">'+value['goods_price']+'</td>';
		text+='<td align="center" bgcolor="#FFFFFF">'+value['all_amount']+'</td>';
		goodsTotal+=parseFloat(value['goods_price']);
		total+=parseFloat(value['all_amount']);
		if(typeof(value['error'])!='undefined'){type = '异常';}
		text+='<td align="center" bgcolor="#FFFFFF">'+type+'</td>';
		text+='</tr>';
		text+='<tr>';
		text+='<td align="center" bgcolor="#FFFFFF">地址</td>';
		text+='<td colspan="11" bgcolor="#FFFFFF">';
		if(value['same_city']=='1')
	    {
		 //同城给省市区
		 text+=value['province']+'-'+value['city']+'-'+value['district'];
		}
		else{
		 //地址
		text+=value['receiving_name_area'];
		}
		text+='</td>';
		text+='</tr>';		
		});		
		text+='<tr>';
		//text+='<td align="center" bgcolor="#FFFFFF">合计</td>';
		//text+='<td colspan="8" bgcolor="#FFFFFF">';
		//text+= total+'(含运费)';
	//	text+='</td>';
	    text+='<td bgcolor="#FFFFFF" align="center">总计</td>';
	    text+='<td bgcolor="#FFFFFF"></td>';
	    text+='<td bgcolor="#FFFFFF"></td>';
	    text+='<td bgcolor="#FFFFFF"></td>';
	    text+='<td bgcolor="#FFFFFF"></td>';
		text+='<td bgcolor="#FFFFFF" align="center">'+T_YP+'</td>';
		text+='<td bgcolor="#FFFFFF" align="center">'+T_TP+'</td>';
		text+='<td bgcolor="#FFFFFF" align="center">'+T_HP+'</td>';
		text+='<td bgcolor="#FFFFFF" align="center">'+goodsTotal+'</td>';
		text+='<td bgcolor="#FFFFFF" align="center">'+total+'</td>';
		text+='<td bgcolor="#FFFFFF"></td>';
		text+='</tr>';
		text+='</table>';
		//
		text+='</td>';
        text+='</tr>';
        text+='</table>';
		//
		return text;
	} 
    /**
	*  财务打印
	**/ 
    function financial(data){
	  var text = '';
	  text='<table width="100%"  height="235" border="0" cellpadding="0" cellspacing="0">';
      text+='<tr>';
      text+='<td colspan="2" align="center">友件存根</td>';
      //text+='<td width="557" align="center">&nbsp;</td>';
      text+='</tr>';
      text+='<tr>';
      text+='<td width="48%"><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
      text+='<tr bgcolor="#000000">';
      text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
      text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">应收款</td>';
      text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
      text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">应收款</td>';
      text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
      text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">应收款</td>';
      text+='</tr>';
     // for(var i=0;i<data.length;i++){
	 var i=0;
	 var totalprice = 0;
	  while(i<30){
		  if(i%3==0){
		   text+='<tr  bgcolor="#000000">';
		  }
		 for(var j=0;j<3;j++){
		  if(i<data.length){
			// console.log(i);
			  text+='<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['logistics_sn']+'</td>';
			  text+='<td height="5" align="center" bgcolor="#FFFFFF">'+data[i]['amount']+'</td>';
			  totalprice+= parseFloat(data[i]['amount']);
		  }
		  else{
			  text+='<td height="15" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
			  text+='<td height="15" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
		  }
		   i = i+1;
		 }
		  if(i%3==0){
		   text+='</tr>';
		  }
	 
		// i++;
	  }
      text+='</table></td>';
      text+='<td width="121">';
	  text+='<table width="100%" height="242" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
      text+='<tr>';
      text+='<td bgcolor="#FFFFFF" align="center">应收款总价</td>';
      text+='</tr>';
      text+='<tr>';
      text+='<td bgcolor="#FFFFFF" align="center">'+totalprice.toFixed(2)+'</td>';
      text+='</tr>';
      text+='<tr>';
      text+='<td bgcolor="#FFFFFF" align="center">实际支付</td>';
      text+='</tr>';
      text+='<tr>';
      text+='<td bgcolor="#FFFFFF" align="center">'+totalprice.toFixed(2)+'</td>';
      text+='</tr>';
      text+='</table></td>';
      text+='<td><p>存</p>';
      text+='<p>根</p></td>';
      text+='</tr>';
      text+='</table>';
      text+='<table width="100%" height="235" border="0" cellpadding="0" cellspacing="0">';
      text+='<tr>';
      text+='<td colspan="2" align="center">友件收据</td>';
     // text+='<td width="537" align="center">&nbsp;</td>';
      text+='</tr>';
      text+='<tr>';
      text+='<td width="48%"><table width="100%" height="235" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
      text+='<tr bgcolor="#000000">';
      text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
      text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">应收款</td>';
      text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
      text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">应收款</td>';
      text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
      text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">应收款</td>';
      text+='</tr>';
      var i=0;
	 var totalprice = 0;
	  while(i<30){
		  if(i%3==0){
		   text+='<tr  bgcolor="#000000">';
		  }
		 for(var j=0;j<3;j++){
		  if(i<data.length){
			 console.log(i);
			  text+='<td height="18" align="center" bgcolor="#FFFFFF">'+data[i]['logistics_sn']+'</td>';
			  text+='<td height="18" align="center" bgcolor="#FFFFFF">'+data[i]['amount']+'</td>';
			  totalprice+= parseFloat(data[i]['amount']);
		  }
		  else{
			  text+='<td height="18" align="center" bgcolor="#FFFFFF"></td>';
			  text+='<td height="18" align="center" bgcolor="#FFFFFF"></td>';
		  }
		   i = i+1;
		 }
		  if(i%3==0){
		   text+='</tr>';
		  }
	 
		// i++;
	  }
      text+='</table></td>';
      text+='<td width="121"><table width="100%" height="235" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
      text+='<tr>';
      text+='<td bgcolor="#FFFFFF" align="center">应收款总价</td>';
      text+='</tr>';
      text+='<tr>';
      text+='<td bgcolor="#FFFFFF" align="center">'+totalprice.toFixed(2)+'</td>';
      text+='</tr>';
      text+='<tr>';
      text+='<td bgcolor="#FFFFFF" align="center">实际支付</td>';
      text+='</tr>';
      text+='<tr>';
      text+='<td bgcolor="#FFFFFF" align="center">'+totalprice.toFixed(2)+'</td>';
      text+='</tr>';
      text+='</table></td>';
      text+='<td><p>收</p>';
      text+='<p>据</p></td>';
      text+='</tr>';
      text+='</table>';	
	  return text;
	}
    function smallReceipt (data) {
        var text = '';
       text='<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000" style="font-size:11px">';
	   text+='<tr>';
	   text+='<td align="center" width="50" bgcolor="#FFFFFF">票号</td>';
	   text+='<td align="center" bgcolor="#FFFFFF">修理厂</td>';
	   text+='<td align="center" bgcolor="#FFFFFF">代收款</td>';
	   text+='<td align="center" width="30" bgcolor="#FFFFFF">运费</td>';	   
	   text+='</tr>';
	   var allGoodsPrice = 0;
	   var allFreight = 0;
	   for(var i=0;i<data.length;i++) {
            var temp = data[i]['logistics_sn'].substr(5,1);
            if(temp == '0') {
               var logistics_sn = data[i]['logistics_sn'].substr(6);
            } else {
                var logistics_sn = data[i]['logistics_sn'].substr(5);
            }
			allGoodsPrice += parseFloat(data[i]['goods_price']);
			var freight = (parseFloat(data[i]['freight']) + parseFloat(data[i]['make_from_price']));
            freight = freight.toString();
			if(freight.indexOf(".")>=0){
			  freight = freight.replace(".", "<br>.<br>")
			}
		   allFreight += parseFloat(data[i]['freight']) + parseFloat(data[i]['make_from_price']) - parseFloat(data[i]['shipping_sale']); 
		   text+='<tr>';
		   text+='<td align="center" bgcolor="#FFFFFF">' + logistics_sn + '</td>';
   		   text+='<td align="center" bgcolor="#FFFFFF">'+data[i]['receiving_name'].substr(0,10)+'</td>';//10个汉字
		   text+='<td align="center" bgcolor="#FFFFFF">' + data[i]['goods_price']+'</td>';
		   text+='<td align="center" bgcolor="#FFFFFF">' + freight + '</td>';
		   text+='</tr>';
        }	
		 text+='<tr>';
		 text+='<td align="center" bgcolor="#FFFFFF">' + data.length + '</td>';
 		 text+='<td align="center" bgcolor="#FFFFFF">&nbsp;</td>';
		 text+='<td align="center" bgcolor="#FFFFFF">' + allGoodsPrice+'</td>';
		 text+='<td align="center" bgcolor="#FFFFFF">' + allFreight + '</td>';
		 text+='</tr>';
		 var myDate = new Date();
	     text+='</table>';
		 text+='<div>';
		 text+=myDate.toLocaleDateString();
         text+='</div>';		
        return text;
    }
    function gerPrinter(){ 
        var iPrinterCount=LODOP.GET_PRINTER_COUNT();
        for(var i=0;i<iPrinterCount;i++){
            console.log(LODOP.GET_PRINTER_NAME(i), LODOP.GET_VALUE('PRINTSETUP_PAGE_WIDTH', i), LODOP.GET_VALUE('PRINTSETUP_PAGE_HEIGHT', i));
        };  
    };
    function getMyValue(strType, oResultOB){
//        var value;
//        if (LODOP.CVERSION) {
//            CLODOP.On_Return = function (TaskID, Value) {
//                if (oResultOB)
//                    value = Value;
//            };
//        }
//
//        var stResult = LODOP.GET_VALUE(strType, "0");
//        if (!LODOP.CVERSION){
//            value = stResult;
//        }
//        return value;
        var LODOP=getLodop(document.getElementById('LODOP_X'),document.getElementById('LODOP_EM')); 
        if (LODOP.CVERSION) CLODOP.On_Return=function(TaskID,Value){if (oResultOB) oResultOB.value=Value;}; 
        var stResult=LODOP.GET_VALUE(strType,"0");
        if (!LODOP.CVERSION) oResultOB.value=stResult; 
        return oResultOB;
    };
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
    if(data==undefined){
    	return '';
    	}
	data = data.replace(/\s+/g,"");
	if(data.length>leng){
	  txt = '...';
	}
    return data.substr(0,leng)+txt;
}
 /**
	*  财务打印2
	**/ 
    function financial_T(data){
      //判断数量
	  var total = data.orders.length;
	  var sum  =   total/30;
   		  var count =  Math.ceil(sum);

	  var text = '';
      for(var z=0;z<count;z++){		  
		  text+='<table width="100%"  height="235" border="0" cellpadding="0" cellspacing="0">';
		  text+='<tr>';
		  text+='<td colspan="2" height="20" align="center">友件存根</td>';
		  //text+='<td width="557" align="center">&nbsp;</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td width="48%"><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
		  text+='<tr bgcolor="#000000">';
		  text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
		  text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">实收款</td>';
		  text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
		  text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">实收款</td>';
		  text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
		  text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">实收款</td>';
		  text+='</tr>';
		 // for(var i=0;i<data.length;i++){
		 var i=0;
		 var totalprice = 0;
		
		  while(i<30){
			  if(i%3==0){
			   text+='<tr  bgcolor="#000000">';
			  }
		  var _num = 0;
		   //  if(num==0){
		//	 }
		//	 else{
		//	  _num  = num+i+1;
		//	 }
			 for(var j=0;j<3;j++){
			  //计算分页后数据
		      var num = 30*z;
			  	 _num = num+i;
			  if(i<(total-num)){//修改i小于剩余数量，下面的i 30的倍数
				// console.log(i);
				  text+='<td height="5" align="center" bgcolor="#FFFFFF">'+data.orders[_num].order_sn+'</td>';
				  text+='<td height="5" align="center" bgcolor="#FFFFFF">'+data.orders[_num].rel_amount+'</td>';
			  }
			  else{
				  text+='<td height="15" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
				  text+='<td height="15" align="center" bgcolor="#FFFFFF">&nbsp;</td>';
			  }
			   i = i+1;
			 }
			  if(i%3==0){
			   text+='</tr>';
			  }
		 
			// i++;
		  }
		  text+='</table></td>';
		  text+='<td width="121">';
		  text+='<table width="100%" height="242" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">编号</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.number+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">日期</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.date+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">交款人</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.receiving+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">收款人</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.user+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">实收金额合计</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.all_amount+'</td>';
		  text+='</tr>';
		  text+='</table></td>';
		  text+='<td><p>存</p>';
		  text+='<p>根</p></td>';
		  text+='</tr>';
		  text+='</table>';
		  text+='<table width="100%" height="235" border="0" cellpadding="0" cellspacing="0">';
		  text+='<tr>';
		  text+='<td colspan="2" height="20" align="center">友件收据</td>';
		 // text+='<td width="537" align="center">&nbsp;</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td width="48%"><table width="100%" height="235" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
		  text+='<tr bgcolor="#000000">';
		  text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
		  text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">实收款</td>';
		  text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
		  text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">实收款</td>';
		  text+='<td width="16%" height="5" align="center" bgcolor="#FFFFFF">票号</td>';
		  text+='<td width="10%" height="5" align="center" bgcolor="#FFFFFF">实收款</td>';
		  text+='</tr>';
		  var i=0;
		 var totalprice = 0;		
		  while(i<30){
			  if(i%3==0){
			   text+='<tr  bgcolor="#000000">';
			  }
			 var _num = 0;
			 //  if(num==0){
				 
			//	 }
			//	 else{
			//	  _num  = num+i+1;
			//	 }
			 for(var j=0;j<3;j++){
			 //计算分页后数据
		      var num = 30*z;			  
			  if(i<(total-num)){
				 //console.log(i);
				   _num = num+i;
				  text+='<td height="18" align="center" bgcolor="#FFFFFF">'+data.orders[_num].order_sn+'</td>';
				  text+='<td height="18" align="center" bgcolor="#FFFFFF">'+data.orders[_num].rel_amount+'</td>';
			  }
			  else{
				  text+='<td height="18" align="center" bgcolor="#FFFFFF"></td>';
				  text+='<td height="18" align="center" bgcolor="#FFFFFF"></td>';
			  }
			   i = i+1;
			 }
			  if(i%3==0){
			   text+='</tr>';
			  }
		 
			// i++;
		  }
		  text+='</table></td>';
		  text+='<td width="121"><table width="100%" height="235" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">编号</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.number+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">日期</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.date+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">交款人</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.receiving+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">收款人</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.user+'</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">实收金额合计</td>';
		  text+='</tr>';
		  text+='<tr>';
		  text+='<td bgcolor="#FFFFFF" align="center">'+data.all_amount+'</td>';
		  text+='</tr>';

		  text+='</table></td>';
		  text+='<td><p>收</p>';
		  text+='<p>据</p></td>';
		  text+='</tr>';
		  text+='</table>';	
	  }
	  return text;

	}
