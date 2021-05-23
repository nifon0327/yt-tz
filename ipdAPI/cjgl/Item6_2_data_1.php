<?php 
	
	$DefaultBgColor=$theDefaultColor;
	$i=1;$today=date("Y-m-d");
	$mySql="SELECT M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,
M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType,M.Ship,M.Operator,T.Type as incomeType,C.Forshort 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
WHERE 1 $SearchRows ORDER BY M.Number DESC";

	$d1=anmaIn("../../download/invoice/",$SinkOrder,$motherSTR);	
	$myResult = mysql_query($mySql." $PageSTR",$link_id);
	
	if($myRow = mysql_fetch_array($myResult))
	{
		do
		{
			$m=1;
			$Id=$myRow["Id"];
			$CompanyId=$myRow["CompanyId"];
			$Number=$myRow["Number"];
			$Forshort=$myRow["Forshort"];
			$InvoiceNO=$myRow["InvoiceNO"];
			$InvoiceFile=$myRow["InvoiceFile"];
			$BoxLable="<div class='redB'>未装箱</div>";
			//检查是否有装箱
			$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
			if($PackingRow=mysql_fetch_array($checkPacking))
			{
			//加密参数
				$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
				$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
				$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='../admin/ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
			$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
			$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"#function+invoiceFile+$InvoiceNO\">查看</a>";
			$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
			$Date=$myRow["Date"];
			$Locks=$myRow["Locks"];
			$Operator=$myRow["Operator"];
			
			include "../model/subprogram/staffname.php";
			//出货金额
			$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
			$Amount=sprintf("%.2f",$checkAmount["Amount"]);
			$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
			if($SubAction==31 && $ShipEstate!=0)
			{
		       //$UpdateIMG="<img src='../images/register.png' width='30' height='30'";
			   //$UpdateClick="onclick='Vefiry(\"$Id\")'";
			   $UpdateIMG="<input type='button' id='shipBtn' name='$UpdateIMG' onclick='Vefiry(\"$Id\",this)' value='出 货'>";
			}
			$chooseStr="<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$Id'>";
		
			$showPurchaseorder="[ + ]";
			$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
		
			/*
			echo"<tr><td class='A0111' align='center' height='25' valign='middle' $ColbgColor>$chooseStr</td>
			<td class='A0101' id='theCel$i' align='center' onClick='ShowOrHide(ListRow$i,theCel$i,$i,\"$Id\");' >$showPurchaseorder</td>";
			echo"<td class='A0101' align='center' >$Number</td>";
			echo"<td class='A0101' align='center'>$Forshort</td>";
			echo"<td class='A0101' align='center'>$InvoiceNO</td>";
			echo"<td class='A0101' align='center'>$InvoiceFile</td>";
			echo"<td class='A0101' align='center'>$BoxLable</td>";
			echo"<td class='A0101' align='center'>$Amount</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$Wise</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			*/
			?>
			
			<tr>
				<td width="30px"><?php  echo $i ?></td>
				<td width="40px" id="<?php  echo "theCel$i" ?>" onClick="ShowOrHide(<?php  echo "ListRow$i" ?>,<?php  echo "theCel$i" ?>,<?php  echo $i ?>, <?php  echo "$Id"?>);" ><?php  echo $showPurchaseorder ?></td>
				<td width="40px"><?php  echo $chooseStr ?></td>
				<td width="100px"><?php  echo $Number ?></td>
				<td width="120px"><?php  echo $Forshort ?></td>
				<td width="120px"><?php  echo $InvoiceNO ?></td>
				<td width="120px"><?php  echo $InvoiceFile ?></td>
				<td width="80px"><?php  echo $BoxLable ?></td>
				<td width="80px"><?php  echo $Amount ?></td>
				<td width="100px"><?php  echo $Date ?></td>
				<td width="120px"><?php  echo $Wise ?></td>
				<td><?php  echo $UpdateIMG ?></td>
			</tr>

		<?php 	
			$i++;	
			echo $ListRow;	
		}
		while ($myRow = mysql_fetch_array($myResult));
		echo "<input type='hidden' id='AllId' name='AllId' value='$i'>";
		//echo "</table>";
	}
	else
	{
		echo"<tr><td colspan='12' align='center' height='30' class='A0111'><div class='redB'>No relevant information</div></td></tr>";
	}
	
?>