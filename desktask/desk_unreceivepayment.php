<?php   
/*
$DataIn.ch1_shipmain$DataIn.ch1_shipsheet$DataIn.trade_object$DataPublic.currencydata$DataIn.cw6_advancesreceived二合一已更新
*/
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";

$tableWidth=970;
$subTableWidth=950;
$i=1;
//未收货款总额
$ShipResult = mysql_query("SELECT SUM( S.Price * S.Qty * D.Rate * M.Sign) AS GatheringSUM
	FROM $DataIn.ch1_shipmain M
	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
	WHERE M.Estate =0 AND cwSign=1",$link_id);
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$AllOrderAmount=sprintf("%.0f",$ShipRow["GatheringSUM"]);

	}
$nowMonth=substr(date("Y-m-d"),0,7);
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="24" colspan="6">未收货款统计</td>
    </tr>

	<tr>
		<td height="24">&nbsp;</td>
   	 <td height="24" colspan="6" align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="575" class="A1111" height="25">&nbsp;</td>
	<td width="95" class="A1101" align="center">未收货款百分比</td>
    <td width="75" class="A1101" align="center">预收货款</td>
    <td width="75" class="A1101" align="center">未收HKD</td>
    <td width="75" class="A1101" align="center">未收USD</td>
    <td width="75" class="A1101" align="center">未收RMB</td>
  </tr>
</table>
<?php   
//读取未结付货款：包手部分结付的单
$ShipResult = mysql_query("SELECT SUM( S.Price*S.Qty*M.Sign) AS Amount,M.CompanyId,C.Forshort,C.Currency,D.Rate
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
WHERE M.Estate =0 AND M.cwSign IN (1,2) GROUP BY M.CompanyId ORDER BY Amount DESC",$link_id);
$Total=0;$Total_1=0;$Total_2=0;$Total_3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$FK_1=0;$FK_2=0;$FK_3=0;$FK_JY=0;
		$CompanyId=$ShipRow["CompanyId"];
		//计算部分结付单的金额
		$CheckPart=mysql_fetch_array(mysql_query("SELECT SUM(-P.Amount*M.Sign) AS GatheringSUM 
		FROM $DataIn.cw6_orderinsheet P
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
		WHERE M.cwSign='2' AND M.CompanyId='$CompanyId'",$link_id));
		$PayedAmount=$CheckPart["GatheringSUM"];
		$Forshort=$ShipRow["Forshort"];
		$Rate=$ShipRow["Rate"];
		$Currency=$ShipRow["Currency"];
		$TempFKSTR="FK_".strval($Currency);
		//预收货款
		$CheckPreJY=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) FK_JY FROM $DataIn.cw6_advancesreceived WHERE CompanyId='$CompanyId' AND Mid='0'",$link_id));
		$FK_JY=$CheckPreJY["FK_JY"];
		$Yshk+=$FK_JY*$Rate;
		$$TempFKSTR=sprintf("%.2f",($ShipRow["Amount"]-$FK_JY+$PayedAmount));
		$TempFKSUM="Total_".strval($Currency);$$TempFKSUM=$$TempFKSUM+$$TempFKSTR;
		$TempRMB=$$TempFKSTR*$Rate;
		$Total=$Total+$TempRMB;
		
		//百分比
		$TempPC=($TempRMB/$AllOrderAmount)*100;
		$TempPC=$TempPC>=1?(round($TempPC)."%"):"&nbsp;";
		
			//传递客户
			/*$DivNum="a";
			$TempId="$CompanyId|$Currency|$DivNum";			
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_unreceivepayment_a\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";*/
		
?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A' bgcolor='#CCCCCC'>
			<td class="A0111" width="575" height="25">&nbsp;<?php    echo $Forshort."(".$CompanyId.")"?></td>
			<td class="A0101" width="95" align="center"><?php    echo $TempPC?></td>
			<td class="A0101" width="75" align="right"><?php    echo zerotospace($FK_JY)?></td>
			<td class="A0101" width="75" align="right"><?php    echo zerotospace($FK_3)?></td>
			<td class="A0101" width="75" align="right"><?php    echo zerotospace($FK_2)?></td>
			<td class="A0101" width="75" align="right"><?php    echo zerotospace($FK_1)?></td>
		</tr>
<?php  
       //echo $HideTableHTML;
       $mySql="SELECT SUM(S.Price*S.Qty*M.Sign) AS Amount,M.Date
                      FROM $DataIn.ch1_shipmain M
                      LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
                     WHERE M.Estate=0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' 
                     GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date";
//echo $mySql;
         $myResult = mysql_query($mySql,$link_id);
          if($myRow = mysql_fetch_array($myResult)){
	             $clientFK_1=0;$clientFK_2=0;$clientFK_3=0;
	         do{
		             $Date=substr($myRow["Date"],0,7);
		            $CheckPart=mysql_fetch_array(mysql_query("SELECT SUM(-P.Amount*M.Sign) AS GatheringSUM 
		            FROM $DataIn.cw6_orderinsheet P
		            LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
		           WHERE M.cwSign='2' AND M.CompanyId='$CompanyId' AND  DATE_FORMAT(M.Date,'%Y-%m')='$Date'",$link_id));
		            $PayedAmount=$CheckPart["GatheringSUM"];
		            $Amount=$myRow["Amount"];
		            $TempFKSTR="clientFK_".strval($Currency);
		            $$TempFKSTR=sprintf("%.2f",$myRow["Amount"]+$PayedAmount);
		            $DivNum=$predivNum.$RowId."b".$i;
		            $TempId="$CompanyId|$Currency|$Date|$DivNum";
		            $showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_unreceivepayment_b\");' 
		            id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		            $clientFK_1=zerotospace($clientFK_1);
		            $clientFK_2=zerotospace($clientFK_2);
		            $clientFK_3=zerotospace($clientFK_3);
                    $date1 = explode("-",$Date);
                    $date2 = explode("-",$nowMonth);
                    $monthSum=abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
                    if($monthSum>=2){
                              $Date="<span class='redB'>$Date</span>";
                              $clientFK_1="<span class='redB'>$clientFK_1</span>";
                              $clientFK_2="<span class='redB'>$clientFK_2</span>";
                              $clientFK_3="<span class='redB'>$clientFK_3</span>";
                      }
                     
		            //echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
			        echo "<tr >
                              <td width='670' colspan='2' class='A0110' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$showPurchaseorder $Date</td>
				             <td width='75' align='right' class='A0100'>$cgQty</td>
				             <td width='75' align='right' class='A0100'>$clientFK_3</td>
				             <td width='75' align='right' class='A0100'>$clientFK_2</td>
				             <td width='75' align='right' class='A0101'>$clientFK_1</td>
			                 </tr>";
		             echo"<tr><td colspan='6'><table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			                  <tr bgcolor='#B7B7B7' ><td height='30'>
				                       <div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div></td>
			                  </tr></table></td></tr>";
		                   $i++;
		               }while ($myRow = mysql_fetch_array($myResult));
	            }
             echo "</table>";
		  $i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr class=''>
    <td class="A0111" height="25" width="575">合 计: (转RMB总额约：<?php    echo zerotospace(number_format(sprintf("%.2f",$Total)))."元"?>)</td>
    <td class="A0101" width="95" align="right">&nbsp;</td>
	<td width="75" class="A0101" align="right"><?php    echo zerotospace($Total_JY)?></td>
    <td class="A0101" width="75" align="right"><?php    echo zerotospace($Total_3)?></td>
    <td class="A0101" width="75" align="right"><?php    echo zerotospace($Total_2)?></td>
    <td class="A0101" width="75" align="right"><?php    echo zerotospace($Total_1)?></td>
  </tr>
 <?php   
$Yshk=sprintf("%.0f",$Yshk);
 ?>
 <tr class=''>
    <td class="A0111" height="25" width="575">实际未收款RMB总额约：<?php    echo zerotospace(number_format(sprintf("%.2f",$Total)))."元"?>(已扣除预收货款:<?php    echo $Yshk?>元)</td>
    <td class="A0101" width="95" align="right">&nbsp;</td>
	<td width="75" class="A0101" align="right">&nbsp;</td>
    <td class="A0101" width="75" align="right">&nbsp;</td>
    <td class="A0101" width="75" align="right">&nbsp;</td>
    <td class="A0101" width="75" align="right">&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>
