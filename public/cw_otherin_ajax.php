<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=900;
echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='25' height='20'></td>
		<td width='80' align='center'>日期</td>
		<td width='110' align='center'>类别</td>
		<td width='70' align='center'>金额</td>		
		<td width='40' align='center'>货币</td>
		<td width='420' align='center'>备注</td>
		<td width='40' align='center'>凭证</td>
		<td width='50' align='center'>状态</td>
         <td width='70' align='center'>操作</td></tr>";
$sListResult = mysql_query("SELECT I.Id,I.Amount,I.Remark,I.Bill,I.Date,I.Estate,I.Locks,I.Operator,C.Symbol,T.Name AS TypeName
FROM $DataIn.cw4_otherin I
LEFT JOIN $DataPublic.currencydata C ON C.Id=I.Currency 
LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=I.TypeId
WHERE  Mid=$Mid",$link_id);
$i=1;
if ($myRow = mysql_fetch_array($sListResult)) {
	do{
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$TypeName=$myRow["TypeName"];
		$BankName=$myRow["Title"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
       switch($myRow["Estate"]){
                    case 1:
                        $Estate="<div class='redB' >未处理</div>";
                        break;
                    case 0:
                        $Estate="<div class='greenB' >已处理</div>";
                        break;
                }
                
		$Locks=$myRow["Locks"];
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/otherin/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="O".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
	
	echo"<tr bgcolor='$theDefaultColor'>
		<td align='right' height='20'>$i</td>";//
		echo"<td  align='center' >$Date</td>";	
		echo"<td  align='center'>$TypeName</td>";//
		echo"<td  align='right' >$Amount</td>";		
		echo"<td  align='center'>$Symbol</td>";
		echo"<td >$Remark</td>";
		echo"<td  align='center'>$Bill</td>";
		echo"<td  align='center'>$Estate</td>";
		echo"<td  align='center'>$Operator</td>";
		echo"</tr>";
		$i=$i+1;
		
	}while ($myRow = mysql_fetch_array($sListResult));
}
else{
	echo"<tr><td height='30' colspan='6'>无相关其他收入</td></tr>";
	}

echo"</table>"."";

?>