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

    function print_tag(num){
    var type = arguments[1] ? arguments[1] : 2;//设置参数a的默认值为1 
	 LODOP.PRINT_INIT("打印货签");
	   LODOP.NewPage();
	   //countnum();
	   //ajax调用写入并读取编码
       ajax_code(num);
	}
	
	//===========================
     //货签函数
     function Tag_Style(code){
	 //console.log(data);
 	  var text = '';	
	    for(var i=0;i<code.length;i++){
          text+='<table width="75%" border="0" cellspacing="0" cellpadding="0">';
          text+='<tr>';
          text+='<th height="30" align="center">友件物流返货帖</th>';
          text+='</tr>';
          text+='<tr>';
          text+='<td><table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000">';
          text+='<tr>';
          text+='<td width="20%" bgcolor="#FFFFFF">收货人:</td>';
          text+='<td colspan="3" bgcolor="#FFFFFF">&nbsp;</td>';
          text+='</tr>';
          text+='<tr>';
          text+='<td bgcolor="#FFFFFF">电&nbsp;&nbsp;&nbsp;&nbsp;话:</td>';
          text+='<td colspan="3" bgcolor="#FFFFFF">&nbsp;</td>';
          text+='</tr>';
          text+='<tr>';
          text+='<td bgcolor="#FFFFFF">发货人:</td>';
          text+='<td colspan="3" bgcolor="#FFFFFF">&nbsp;</td>';
          text+='</tr>';
          text+='<tr>';
          text+='<td bgcolor="#FFFFFF">电&nbsp;&nbsp;&nbsp;&nbsp;话:</td>';
          text+='<td colspan="3" bgcolor="#FFFFFF">&nbsp;</td>';
          text+='</tr>';
          text+='<tr>';
          text+='<td bgcolor="#FFFFFF">代收款:</td>';
          text+='<td width="35%" bgcolor="#FFFFFF">&nbsp;</td>';
          text+='<td width="15%" bgcolor="#FFFFFF">件数:</td>';
          text+='<td width="27%" bgcolor="#FFFFFF">&nbsp;</td>';
          text+='</tr>';
          text+='<tr>';
          text+='<td bgcolor="#FFFFFF">司机名:</td>';
          text+='<td bgcolor="#FFFFFF">&nbsp;</td>';
          text+='<td bgcolor="#FFFFFF">日期:</td>';
          text+='<td bgcolor="#FFFFFF">&nbsp;</td>';
          text+='</tr>';
          text+='<tr>';
          text+='<td colspan="4" align="center" bgcolor="#FFFFFF">西部';
          text+='<input type="checkbox" name="checkbox" id="checkbox" />';
          text+='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;塔湾';
          text+='<input type="checkbox" name="checkbox2" id="checkbox2" />';
          text+='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;瑞盛';
          text+='<input type="checkbox" name="checkbox3" id="checkbox3" /></td>';
          text+='</tr>';
          text+='</table></td>';
          text+='</tr>';
          text+='<tr>';
          text+='<td align="center">编码:'+code[i]+'</td>';
          text+='</tr>';
          text+='</table>';     
		}
		 return text;
	}
    function ajax_code(num){
		
	        var data = {
               'num':num,
           };
           $.ajax({
                type: "post",
                url:'?r=print/ajax-get-code',
                data:data,
                dataType: 'json',
                success:function(data){
					//console.log(data);
                	if(data.code==200){	
					  // LODOP.SET_PRINT_COPIES(num);
					   //===============================================================
					   LODOP.SET_PRINT_PAGESIZE(1,900,600,"CreateCustomPage");
					   //===============================================================
					   LODOP.ADD_PRINT_HTM(15,10,'100mm','100mm',Tag_Style(data.datas));
						 LODOP.SET_PRINTER_INDEX('WL_HUOTIE'); 
						// LODOP.PREVIEW();
						 LODOP.PRINT();				
					}
                     else{
                      return ;
                    }
				}
           })
	}
