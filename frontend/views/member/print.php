<?php //echo '<pre>';print_r($orderList);die;?>
<input id="print" type="button" value="打印" style="width:50px;margin:0px auto;display:block;">
<div id="print_html" style="width:770px;margin:10px auto;">
<?php foreach($orderList as $key => $value){?>
<div style="margin-bottom:20px;">
<table width="770" border="0" cellpadding="0" cellspacing="0">
<tr>
<td  colspan="3" align="center"><b>友件网物流货运送货单</b></td>
<td width="270" rowspan="11" valign="middle">
<p>友</p>
<p>件</p>
<p>网</p>
</td>
</tr>
<tr>
<td width="360" height="30" colspan="1">货号：<?=$value['goods_sn']?></td>
<td width="220" height="30" align="left" colspan="1"><?= date('Y-m-d H:i:s',$value['add_time'])?></td>
<td width="180" height="30" colspan="1" align="center" height="5">票号：<?=$value['logistics_sn']?></td>
</tr>
<tr>
<td colspan="4">
<table width="75%" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
<tr bgcolor="#000000">
<td width="12%" height="5" align="center" bgcolor="#FFFFFF">收货人</td>
<td width="19%" height="5" align="center" bgcolor="#FFFFFF"><?=$value['receiving_name']?></td>
<td width="10%" height="5" align="center" bgcolor="#FFFFFF">电话</td>
<td width="10%" height="5" colspan="2" align="center" bgcolor="#FFFFFF"><?=$value['receiving_phone']?></td>
<td width="9%" height="5" align="center" bgcolor="#FFFFFF">货名</td>
<td width="8%" height="5" colspan="2" align="center" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr  bgcolor="#000000">
<td height="5" align="center" bgcolor="#FFFFFF">发货人</td>
<td height="5" align="center" bgcolor="#FFFFFF"><?=$value['member_name']?></td>
<td height="5" align="center" bgcolor="#FFFFFF">电话</td>
<td height="5" colspan="2" align="center" bgcolor="#FFFFFF"><?=$value['member_phone']?></td>
<td height="5" align="center" bgcolor="#FFFFFF">件数</td>
<td height="5" colspan="2" align="center" bgcolor="#FFFFFF"><?=$value['goods_num']?></td>
</tr>
<tr  bgcolor="#000000">
<td height="5" align="center"rowspan="2" bgcolor="#FFFFFF">地址</td>
<td colspan="5" rowspan="2" align="center" bgcolor="#FFFFFF"><?=$value['receiving_name_area']?></td>
<td width="12%" height="5" align="center" bgcolor="#FFFFFF">保价金额</td>
<td width="10%" height="5" align="center" bgcolor="#FFFFFF">保价费</td>
</tr>
<tr  bgcolor="#000000">	
<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>
<td height="5" align="center" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr  bgcolor="#000000">
<td height="5" align="center" bgcolor="#FFFFFF">退货货值</td>
<td height="5"   align="center" bgcolor="#FFFFFF"><?=$value['goods_price']?></td>
<td height="5" colspan="4" align="center" bgcolor="#FFFFFF">
运费  <?=number_format(floatval($value['freight']+$value['make_from_price']),2)?>
</td>
<?php switch($value['shipping_type']){
   case '1':
       $shipping_type = '提付';
       break;
   case '2':
	   $shipping_type = '回付';
       break;
   case '3':
	   $shipping_type = '已付';
       break;
 }
?>
 <td height="5" align="center" colspan="3" bgcolor="#FFFFFF"><?=$shipping_type?></td>
</tr>
<tr  bgcolor="#000000">
<td height="5" align="center" bgcolor="#FFFFFF">合计大写</td>
<td height="5" colspan="5" align="center" bgcolor="#FFFFFF"><?=$value['All_amount']?></td>
<td height="5" align="center" bgcolor="#FFFFFF">合计</td>
<td height="5" align="center" bgcolor="#FFFFFF"><?=$value['all_amount']?></td>
</tr>
<tr  bgcolor="#000000">
<td colspan="5" rowspan="2" align="center" bgcolor="#FFFFFF">&nbsp;</td>
<td height="50" align="center" bgcolor="#FFFFFF">备注</td>
<td height="50" colspan="2" align="center" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr  bgcolor="#000000">
<td height="5" align="center" bgcolor="#FFFFFF">操作员</td>
<td height="5" colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
<?php }?>
<script>
window.onload = function(){
// 	var print = document.getElementById("print");
// 	print.onclick=function(){
		
// 	}
	var printData = document.getElementById("print_html").innerHTML;
	window.document.body.innerHTML = printData
	window.print();	
}
// var printData = document.getElementById("print_html").innerHTML;
// window.document.body.innerHTML = printData
// window.print();
</script>