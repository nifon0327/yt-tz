<?php   
//*********总务采购费用OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=1000;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
$Th_Col="选项|40|序号|40|申购日期|70|申购物品名称|100|图片|40|数量|50|单位|50|单价|50|金额|60|供应商|80|采购说明|260|采购人|60|请款日期|70|凭证|40";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='70' align='center'>申购日期</td>
		<td width='100' align='center'>申购物品名称</td>
		<td width='40' align='center'>图片</td>
		<td width='50' align='center'>数量</td>
		<td width='50' align='center'>单位</td>
		<td width='50' align='center'>单价</td>
		<td width='60' align='center'>金额</td>
		<td width='80' align='center'>供应商</td>
		<td width='260' align='center'>采购说明</td>
		<td width='60' align='center'>采购人</td>
		<td width='70' align='center'>请款日期</td>
		<td width='40' align='center'>凭证</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND DATE_FORMAT(S.Date,'%Y-%m')='$MonthTemp'";

$mySql="SELECT S.Id,S.Unit,S.Price,S.Qty,T.TypeName,S.cgSign,S.Remark,S.Estate,M.Name AS Buyer,S.Bill,S.Locks,S.qkDate,S.Operator,T.Id AS TId,T.Attached,C.cName,C.Linkman,C.Tel,S.Date
FROM $DataIn.zw3_purchases S 
LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=S.TypeId
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId
LEFT JOIN $DataIn.retailerdata C ON C.Id=S.Cid
WHERE 1 $SearchRows order by S.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
$m=1;
		$Id=$myRow["Id"];
		$Qty=$myRow["Qty"];
		$Unit=$myRow["Unit"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$TypeName=$myRow["TypeName"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":trim($myRow["Remark"]);
		$qkDate=$myRow["qkDate"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		$Buyer=$myRow["Buyer"];
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/zwbuy/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="Z".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		$Locks=1;
		$TId=$myRow["TId"];
		$Attached=$myRow["Attached"];
		$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached="Z".$TId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		$cName=$myRow["cName"];
                if ($cName!=""){
                    $cName="<span title='联系人:" . $myRow["Linkman"] . "&#10 联系电话:" . $myRow["Tel"] . "'>$cName</span>";
                }
                else{
                    $cName="&nbsp;";
                }
          if(floor($Qty)==$Qty) { $Qty=floor($Qty); }

         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='70' align='center'>$Date</td>
				<td width='100' align='center'>$TypeName</td>
				<td width='40' align='center'>$Attached</td>
				<td width='50' align='center'>$Qty</td>
                <td width='50' align='center'> $Unit</td>
				<td width='50' align='center'>$Price</td>
				<td width='60' align='center'>$Amount</td>
				<td width='80' align='center' >$cName</td>
                <td width='260' > $Remark</td>
				<td width='60' align='center'>$Buyer</td>
				<td width='70' align='center'>$qkDate</td>
				<td width='40' align='center' >$Bill</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>