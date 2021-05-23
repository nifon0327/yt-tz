<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 未收货款统计");
//echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=1160;
$subTableWidth=1100;
$subMonth=7;//统计月份数量
$i=1;
$ShipResult = mysql_query("SELECT SUM( S.Price * S.Qty * D.Rate * M.Sign) AS GatheringSUM
	FROM $DataIn.ch1_shipmain M
	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
	WHERE M.Estate =0 AND cwSign=1",$link_id);
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$AllOrderAmount=sprintf("%.0f",$ShipRow["GatheringSUM"]);

	}
$MResult=mysql_query("SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
        WHERE M.Estate=0 AND M.cwSign IN (1,2) 
        GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY DATE_FORMAT(M.Date,'%Y-%m')",$link_id);
while($MRow=mysql_fetch_array($MResult)){
       $Marray[]=$MRow["Month"];
}
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="25" >未收货款统计</td>
    </tr>
	<tr>
   	 <td align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="50" height="25"class="A1111" align="center">客户ID</td>
    <td width="90" height="25"class="A1100" align="center">&nbsp;客&nbsp;&nbsp;户</td>
    <td class="A1101" width="25" >&nbsp;</td>
     <?php 
         //$totalMonth=array();
        // $CheckMonth=date("Y-m");		//当前月第一天
         //$sub_M=$subMonth-1;
        // $StratMonth=date("Y-m",strtotime("$CheckMonth -$sub_M month"));//计算的起始日期
        $subMonth=count($Marray);
         for($i=0;$i<$subMonth;$i++){
              //$tmpMonth=date("Y-m",strtotime(" $StratMonth +$i month"));
             $tmpMonth=$Marray[$i];
              $monthTotal[$tmpMonth]=0;
              $mAmount[$tmpMonth]=0;
	         echo "<td width='80' class='A1101' align='center'>$tmpMonth</td>";
         }
    ?>

	<td width="80" class="A1101" align="center">预收货款</td>
	<td width="80" class="A1101" align="center">未收USD</td>
	<td width="80" class="A1101" align="center">未收RMB</td>
	<td width="80" class="A1101" align="center">合计(RMB)</td>
  </tr>
</table>
<?php 
//读取未收货款
$ShipResult = mysql_query("SELECT * FROM (SELECT SUM( S.Price*S.Qty*M.Sign) AS Amount,M.CompanyId,C.Forshort,C.Currency,C.SaleMode,D.Rate,SUM( S.Price*S.Qty*M.Sign*D.Rate) AS AmountOrderby
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
WHERE M.Estate =0 AND M.cwSign IN (1,2) GROUP BY M.CompanyId 
UNION ALL 
SELECT 0 AS Amount,M.CompanyId,C.Forshort,C.Currency,C.SaleMode,D.Rate,0 AS AmountOrderby 
FROM $DataIn.cw6_advancesreceived M
LEFT JOIN $DataIn.trade_object C ON  M.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.currencydata D   ON C.Currency=D.Id
WHERE M.Mid=0 GROUP BY CompanyId)A  
GROUP BY A.CompanyId  ORDER BY A.AmountOrderby DESC",$link_id);
$Total=0;$Total_1=0;$Total_2=0;$Total_3=0;$Total_JY=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$FK_1=0;$FK_2=0;$FK_3=0;$FK_JY=0;$FK_RMB=0;
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$ShipRow["Forshort"];
		$SaleMode = $ShipRow["SaleMode"];
		$Forshort.= $SaleMode==1?"<img src='../images/salein.jpg' width='18' height='18'>":"";

		$Rate=$ShipRow["Rate"];
		$Currency=$ShipRow["Currency"];
        if($Currency==1)$symbol="￥";
        else $symbol="";
        //部分收款
		$CheckPart1=mysql_fetch_array(mysql_query("SELECT SUM(-P.Amount*M.Sign) AS GatheringSUM 
		FROM $DataIn.cw6_orderinsheet P
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
		WHERE M.cwSign='2' AND M.CompanyId='$CompanyId'",$link_id));
		$PayedAmount=$CheckPart1["GatheringSUM"];
		$TempFKSTR="FK_".strval($Currency);
		//预收货款
		$CheckPreJY=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) FK_JY FROM $DataIn.cw6_advancesreceived WHERE CompanyId='$CompanyId' AND Mid='0'",$link_id));
		$FK_JY=$CheckPreJY["FK_JY"];
		$Yshk+=$FK_JY*$Rate;
		$$TempFKSTR=sprintf("%.2f",($ShipRow["Amount"]+$PayedAmount));//-$FK_JY
		$TempFKSUM="Total_".strval($Currency);
        $$TempFKSUM=$$TempFKSUM+$$TempFKSTR;
		$TempRMB=$$TempFKSTR*$Rate;
		$Total=$Total+$TempRMB;	
		//百分比
		$TempPC=$AllOrderAmount!=0?($TempRMB/$AllOrderAmount)*100:0;
		$TempPC=$TempPC>=1?"<span class='greenB'>".(round($TempPC)."%")."</span>":"&nbsp;";

		
		$MonthResult= mysql_query("SELECT D.Rate,SUM(S.Price*S.Qty*M.Sign) AS Amount,DATE_FORMAT(M.Date,'%Y-%m') AS Month
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
        WHERE M.Estate=0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' 
        GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
	    while($MonthRow=mysql_fetch_array($MonthResult)){
		          $Month=$MonthRow["Month"];
		          $Rate=$MonthRow["Rate"];
		          $CheckPart2=mysql_fetch_array(mysql_query("SELECT SUM(-P.Amount*M.Sign) AS GatheringSUM 
		          FROM $DataIn.cw6_orderinsheet P
		          LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
		          WHERE M.cwSign='2' AND M.CompanyId='$CompanyId' AND  DATE_FORMAT(M.Date,'%Y-%m')='$Month'",$link_id));
		          $PaymonthAmount=$CheckPart2["GatheringSUM"]==""?0:$CheckPart2["GatheringSUM"];
		          $mAmount[$Month]=$MonthRow["Amount"]+$PaymonthAmount;
		          $monthTotal[$Month]+=$mAmount[$Month]*$Rate;
	         }

?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" width="50" height="25" align="center"><?php  echo $CompanyId?></td>
			<td class="A0100" width="90" height="25">&nbsp;<?php  echo $showPurchaseorder?>&nbsp;<?php  echo $Forshort?></td>
            <td class="A0101" width="25" align="right">&nbsp;<?php echo $TempPC ?></td>
	<?php
		$DivNum="a";
	   	for($j=0;$j<$subMonth;$j++){
	           $tmpMonth=$Marray[$j];
               //$tmpMonth=date("Y-m",strtotime("$StratMonth +$j month"));
               $tmpAmount=$mAmount[$tmpMonth]==0?"&nbsp;":$symbol.number_format(sprintf("%.2f",$mAmount[$tmpMonth]),2);                 
	           $TempId="$CompanyId|$tmpMonth|$DivNum$i";
	           $onClickStr="onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_clientfkcount_a\",\"desktask\");'";
             
               if($mAmount[$tmpMonth]>=500000)$tmpAmount="<span class='redB' title='超过50万'>$tmpAmount</span>";
               else{
                       if($mAmount[$tmpMonth]>=100000)$tmpAmount="<span class='yellowB' title='10-50万'>$tmpAmount</span>";
                      }
               $tmpAmount=$tmpAmount<0?"<span class='purpleB'>$tmpAmount</span>":$tmpAmount;
               echo "<td width='80' class='A0101' align='right' $onClickStr>$tmpAmount</td>";
               $mAmount[$tmpMonth]=0;               
               $HideTableHTML="<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				      <tr bgcolor='#B7B7B7'><td class='A0111' height='30'>
						    <br>
							    <div id='HideDiv_$DivNum$i' width='$subTableWidth' align='center'>&nbsp;</div>
						    <br>
					     </td>
				      </tr>
			      </table>";
            }
         if($FK_JY>0)$FK_JY="<span class='greenB'>$FK_JY</span>";
         else $FK_JY=zerotospace($FK_JY);
            if($FK_3!=0){
                        if($FK_3>=500000)$FK_3="<span class='redB' title='超过50万'>".number_format($FK_3,2)."</span>";
                        else {
                                  if($FK_3>=100000)$FK_3="<span class='yellowB' title='10-50万'>".number_format($FK_3,2)."</span>";
                                  else $FK_3=number_format($FK_3,2);
                                  }
                        }
            else $FK_3=zerotospace($FK_3);
            if($FK_2!=0){
                        if($FK_2>=500000)$FK_2="<span class='redB' title='超过50万'>".number_format($FK_2,2)."</span>";
                        else {
                                  if($FK_2>=100000)$FK_2="<span class='yellowB' title='10-50万'>".number_format($FK_2,2)."</span>";
                                  else $FK_2=number_format($FK_2,2);
                                  }
                        }
            else $FK_2=zerotospace($FK_2);
            if($FK_1!=0){
                        if($FK_1>=500000)$FK_1="<span class='redB' title='超过50万'>".$symbol.number_format($FK_1,2)."</span>";
                        else {
                                  if($FK_1>=100000)$FK_1="<span class='yellowB' title='10-50万'>".$symbol.number_format($FK_1,2)."</span>";
                                  else $FK_1=$symbol.number_format($FK_1,2);
                                  }
                        }
            else $FK_1=zerotospace($FK_1);
	?>		
			<td class="A0101" width="80" align="right"><?php echo $FK_JY ?></td><!-- 预收货款-->
			<td class="A0101" width="80" align="right"><?php echo $FK_2?></td><!-- USD-->
			<td class="A0101" width="80" align="right"><?php echo $FK_1?></td><!-- RMB-->
			<td class="A0101" width="80" align="right" bgcolor='#EEEEEE'><?php echo"￥" . number_format($TempRMB) ?></td><!-- RMB-->
		</tr>
	</table>
<?php 
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
?>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr bgcolor='#EEEEEE'>
    <td class="A0110" height="25" width="51">&nbsp;</td>
    <td class="A0100" height="25" width="90">合 计:</td>
    <td class="A0101" width="25" >&nbsp;</td>
    <?php
  for($i=0;$i<$subMonth;$i++){
               $tmpMonth=$Marray[$i];
               echo "<td width='80' class='A0101' align='right'>￥".number_format($monthTotal[$tmpMonth])."</td>";
          }
         // $Total_3=$Total_3==0?"&nbsp;":number_format($Total_3,2);
          $Total_2=$Total_2==0?"&nbsp;":number_format($Total_2,2);
          $Total_1=$Total_1==0?"&nbsp;":"￥".number_format($Total_1,2);
   ?>
<td class="A0101" width="80" align="right"><?php echo "￥" . number_format($Yshk)?></td><!-- 预收货款-->
<td class="A0101" width="80" align="right"><?php echo $Total_2?></td><!-- USD-->
<td class="A0101" width="80" align="right"><?php echo $Total_1?></td><!-- RMB-->
<td class="A0101" width="80" align="right"><b><?php echo "￥" . number_format($Total)?></b></td><!-- RMB-->
</tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr  height="25" class=''><td class="A0111" colspan="12">实际未收款RMB总额约：<?php    echo zerotospace(number_format(sprintf("%.2f",$Total-$Yshk)))."元"?>(已扣除预收货款:<?php    echo $Yshk?>元)=损益表的:货款(invoice)+debit note-客户扣款(credit note)</td></tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="50" height="25"class="A0111" align="center">客户ID</td>
    <td width="90" height="25"class="A0100" align="center">&nbsp;客&nbsp;&nbsp;户</td>
    <td class="A0101" width="25" >&nbsp;</td>
    <?php 
         $CheckMonth=date("Y-m");		//当前月第一天
        // $StratMonth=date("Y-m",strtotime("$CheckMonth -$sub_M month"));//计算的起始日期
         for($i=0;$i<$subMonth;$i++){
              $tmpMonth=$Marray[$i];
	         echo "<td width='80' class='A0101' align='center'>$tmpMonth</td>";
         }
    ?>
	<td width="80" class="A0101" align="center">预收货款</td>
	<td width="80" class="A0101" align="center">未收USD</td>
	<td width="80" class="A0101" align="center">未收RMB</td>
	<td width="80" class="A0101" align="center">合计(RMB)</td>
  </tr>
</table>
</form>
</body>
</html>

<script language="JavaScript" type="text/JavaScript">
function SandH(divNum,RowId,f,TempId,ToPage,FromDir){
	     var e=eval("HideTable_"+divNum+RowId);
//alert("HideTable_"+divNum+RowId)
	    e.style.display=(e.style.display=="none")?"":"none";
		if(TempId!=""){	
		 	if(FromDir !=null && FromDir!="" ){
				var url="../"+FromDir+"/"+ToPage+"_ajax.php?TempId="+TempId+"&RowId="+RowId;
			}
			else{
			     if (FromDir ==null && ToPage.indexOf("desk_")!=-1){//来自桌面
				      var url="../desktask/"+ToPage+"_ajax.php?TempId="+TempId+"&RowId="+RowId;
			     }
			     else{
				      var url="../admin/"+ToPage+"_ajax.php?TempId="+TempId+"&RowId="+RowId;
				   }
			}
		　	var show=eval("HideDiv_"+divNum+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
	}
</script>
