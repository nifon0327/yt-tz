<?php
//23	总务采购费用
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.zw3_purchasem A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='80' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='70' class='A1101'>请款日期</td>
<td width='120' class='A1101'>申购物品名称</td>
<td width='40' class='A1101'>单位</td>
<td width='40' class='A1101'>数量</td>
<td width='80' class='A1101'>单价</td>
<td width='80' class='A1101'>金额</td>
<td width='300' class='A1101'>采购说明</td>
<td width='40' class='A1101'>凭证</td>


</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/zwbuy/",$SinkOrder,$motherSTR);
	$d2=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$djAmount=$checkRow["djAmount"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/zwbuy/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT 
	S.Id,S.Mid,S.Unit,S.Price,S.Qty,T.TypeName,S.cgSign,S.Remark,S.Estate,P.Name AS Buyer,S.Bill,S.Locks,S.qkDate,S.Operator,T.Id AS TId,T.Attached
 	FROM $DataIn.zw3_purchases S
	LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=S.TypeId
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.BuyerId
	WHERE  S.Mid='$Mid' ORDER BY S.Date DESC
	",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//结付日期
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";							//凭证
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//结付备注
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//结付总额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";					//结付银行	
		$j=1;
		do{
			//结付明细数据
			$Id=$checkSheetRow["Id"];
			$Qty=$checkSheetRow["Qty"];
			$Unit=$checkSheetRow["Unit"];
			$Price=$checkSheetRow["Price"];
			$Amount=sprintf("%.2f",$Qty*$Price);
			$TypeName=$checkSheetRow["TypeName"];
			$Remark=trim($checkSheetRow["Remark"])==""?"&nbsp;":trim($checkSheetRow["Remark"]);
			$qkDate=$checkSheetRow["qkDate"];
			$Operator=$checkSheetRow["Operator"];
			include "../model/subprogram/staffname.php";
			$Buyer=$checkSheetRow["Buyer"];
			$Bill=$checkSheetRow["Bill"];
			if($Bill==1){
				$Bill="Z".$Id.".jpg";
				$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
				$Bill="<span onClick='OpenOrLoad(\"$d\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
				}
			else{
				$Bill="&nbsp;";
				}
			$TId=$checkSheetRow["TId"];
			$Attached=$checkSheetRow["Attached"];
			
			if($Attached==1){
				$Attached="Z".$TId.".jpg";
				$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
				$TypeName="<span onClick='OpenOrLoad(\"$d2\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$TypeName</span>";
				}
			if(floor($Qty)==$Qty) { $Qty=floor($Qty); }

			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101' align='center'>$qkDate</td>";
			echo"<td class='A0101'>$TypeName</td>";
			echo"<td class='A0101' align='center'>$Unit</td>";
			echo"<td class='A0101' align='right'>$Qty</td>";
			echo"<td  class='A0101' align='right'>$Price</td>";
			echo"<td  class='A0101' align='right'>$Amount</td>";
			echo"<td  class='A0101'>$Remark</td>";
			echo"<td  class='A0101' align='center'>$Bill</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>