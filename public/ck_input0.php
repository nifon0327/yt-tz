<?php 
//电信-zxq 2012-08-01
?>
<html>
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/pos/pos1.css'>
</head>
<body topMargin=0 scroll=no marginwidth="0" marginheight="0" >
<table border="1" cellspacing="0" style="height:480px;width:272px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
  <tr bgcolor="#999999">
    <td width="27" height="25" align="center" class="A1111">ID</td>
	<td width="150" align="center" class="A1101">流水号</td>
    <td align="center" class="A1101">数量</td>
  </tr>
  <tr>
    <td colspan="3" align="center" class="A0111">
	<div style='width:272;height:422;overflow-x:hidden;overflow-y:scroll'>
	<table  border='0' cellspacing='0' id='ListTable$i' style='width:252px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<?php 
	for($i=1;$i<=30;$i++){
		echo"<tr>
    	<td width='25' height='25' align='center' class='A0101'>$i</td>
  		<td width='150' class='A0101'><input name='text1' type='text' id='text1' value='20091213010203' size='22' maxlength='14' class='StockId'></td>
    	<td class='A0101'><input name='text1' type='text' id='text1' value='12345678' size='9' class='QtyRight'></td>
  		</tr>";
		}
	?>
	</table>
	</div>
	</td>
  </tr>
  <tr><td height="32" colspan="3" class="A0111">
  	<table border="0" cellspacing="0" width="100%" height="100%" bgcolor="#999999"><tr>
		<td align="center" class="A0101"><div onDblClick="javascript:window.close();">关闭</div></td>
		<td align="center" class="A0101"><div onclick="javascript:window.location.reload();">刷新</div></td>
		<td align="center" class="A0100"><div>提交</div></td>
	</tr></table>
	</td>
  </tr>
</table>
</body>