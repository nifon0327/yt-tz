<?php   
//EUR专用模板  //二合一已更新
//$TableFontSize=8;
//$Priceterm=$Priceterm."BBBBB";
switch($InvoiceModel){	
	case 2:     //简体中文
		$N_SUBTOTAL="小计";
		$N_TOTAL="合计";
		$N_Currency="币种  :";		
		break;
	case 3:     //繁体中文
		$N_SUBTOTAL="小計";
		$N_TOTAL="合計";
		$N_Currency="幣别  :";
		break;
	default :   //其它英文版的
		$N_SUBTOTAL="SUBTOTAL";
		$N_TOTAL="TOTAL";
		$N_Currency="Currency  :";
		break;
}

//这两个table必须同时设定，$$eurTableNo 为了高度,目的为了穿透高度，$eurTableNoTotal是直实的数字，为了显示，两个格式，第一行是为了统计高度,第三行是银行高度预留($MaxContainY-$最后一页度:254-240)
$$eurTableNo=" 
<table  border=0 >
<tr  repeat>
<td width=29  align=left height=$RowsHight valign=middle >&nbsp;</td>
<td width=26 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=26 align=right valign=middle ></td></tr>
<tr >
<td width=29  align=left height=14 valign=middle ></td>
<td width=26 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=26 align=right valign=middle ></td></tr>
</table>";  //为了给签名拉开行距

$eurTableNoTotal=" 
<table  border=0 >
<tr bgcolor=#E8F5FC repeat>
<td width=66 align=left height=$RowsHight  valign=middle >DELIVERY COST:</td>	
<td width=18 align=left valign=middle >VAT:</td>			
<td width=24 align=right valign=middle ></td>	
<td width=31 align=right valign=middle >TOTAL:</td>	
<td width=14 align=right valign=middle ></td>
<td width=14 align=right valign=middle ></td>
<td width=17 align=right valign=middle ></td></tr>
</table>";


$$ChinaTableNo=" 
<table  border=0 >
<tr  repeat>
<td width=66 align=left height=$RowsHight  valign=middle ></td>	
<td width=18 align=left valign=middle ></td>			
<td width=24 align=right valign=middle ></td>	
<td width=31 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>
<td width=14 align=right valign=middle ></td>
<td width=17 align=right valign=middle ></td></tr>
<tr >
<td width=66 align=left  valign=middle ></td>	
<td width=18 align=left  valign=middle ></td>			
<td width=24 align=right valign=middle ></td>	
<td width=31 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>
<td width=14 align=right valign=middle ></td>
<td width=17 align=right valign=middle ></td></tr>
</table>";  //为了给签名拉开行距



$ChinaTableNoTotal=" 
<table  border=0 >
<tr  repeat>
<td width=11  align=left height=$RowsHight valign=middle colspan=2>合计:</td>
			<td width=18  align=center valign=middle ></td>
			<td width=33 align=center valign=middle style=bold></td>	
			<td width=20 align=center valign=middle style=bold></td>
			<td width=20 align=center valign=middle style=bold></td>			
			<td width=20 align=center valign=middle style=bold></td>
			<td width=15 align=center valign=middle style=bold></td>	
			<td width=15 align=center valign=middle style=bold></td>
			<td width=30 align=center valign=middle style=bold></td></tr></table>";


?>