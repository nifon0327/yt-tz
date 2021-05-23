<?php  
//其他收入 
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=1140;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='70' align='center'>日期</td>
		<td width='150' align='center'>类别</td>
		<td width='80' align='center'>金额</td>
		<td width='50' align='center'>货币</td>
		<td width='120' align='center'>结付银行</td>
		<td width='450' align='center'>备注</td>
		<td width='40' align='center'>凭证</td>
		<td width='50' align='center'>状态</td>
		<td width='60' align='center'>操作</td>
	</tr></table>";
$SearchRows=" AND I.Estate='3' AND DATE_FORMAT(I.PayDate,'%Y-%m')='$MonthTemp'";
$mySql="SELECT I.Id,I.Amount,I.Remark,I.PayDate,I.Estate,I.Locks,I.Operator,C.Symbol,T.Name AS TypeName 
FROM $DataIn.cw4_otherinsheet I
LEFT JOIN $DataPublic.currencydata C ON C.Id=I.Currency 
LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=I.TypeId
WHERE 1 $SearchRows
ORDER BY I.PayDate DESC,I.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$PayDate=$myRow["PayDate"];
		$TypeName=$myRow["TypeName"];
		$BankName=$myRow["Title"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		//$Estate=$myRow["Estate"]==1?"<div class='redB'>未核</div>":"<div class='greenB'>已核</div>";
                switch($myRow["Estate"])
                {
                    case 1:
                        $Estate="<div class='redB'>未审核</div>";
                        break;
                    case 2:
                        $Estate="<div class='redB'>未结付</div>";
                        break;
                    case 0:
                        $Estate="<div class='greenB'>已结付</div>";
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
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='70' align='center'>$PayDate</td>
				<td width='150' align='center'>$TypeName</td>
				<td width='80' align='center'>$Amount</td>
				<td width='50' align='center'>$Symbol</td>
                <td width='120' > $BankName</td>
				<td width='450' >$Remark</td>
				<td width='40' align='center'>$Bill</td>
				<td width='50' align='center' >$Estate</td>
				<td width='60' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>