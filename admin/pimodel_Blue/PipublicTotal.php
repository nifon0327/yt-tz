<?php   


//这两个table必须同时设定，$$eurTableNo 为了高度,目的为了穿透高度，$eurTableNoTotal是直实的数字，为了显示，两个格式，第一行是为了统计高度,第三行是银行高度预留($MaxContainY-$最后一页度:254-240)
$$eurTableNo=" 
<table  border=0 >
<tr  repeat>
<td width=29  align=left height=$RowsHight valign=middle >&nbsp;</td>
<td width=35 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td></tr>
<tr >
<td width=29  align=left height=14 valign=middle ></td>
<td width=35 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td></tr>
</table>";  //为了给签名拉开行距

$eurTableNoTotal=" 
<table  border=0 >
<tr bgcolor=#E8F5FC repeat>
<td width=29  align=left height=$RowsHight valign=middle >TOTAL:</td>
<td width=71 align=left valign=middle  ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td>
<td width=17 align=left valign=middle > </td>
<td width=22 align=left valign=middle > </td>	
</tr></table>";

$ChinaTableNoTotal=" 
<table  border=0 >
<tr  repeat>
<td width=29  align=left height=$RowsHight valign=middle >合计:</td>
<td width=71 align=left valign=middle  ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td>
<td width=17 align=left valign=middle > </td>
<td width=22 align=left valign=middle > </td>	
</tr></table>";


?>