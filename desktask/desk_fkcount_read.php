<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 未付货款统计");
//echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=950;
$subTableWidth=910;
$subMonth=6;//统计月份数量
$i=1;
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="25" >未付货款统计</td>
    </tr>
	<tr>
   	 <td align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="50" height="25"class="A1111" align="center">供应商ID</td>
    <td width="200" class="A1100" align="center">&nbsp;供&nbsp;应&nbsp;商</td>
    <td class="A1101" width="25" >&nbsp;</td>
    <?php 
         $totalMonth=array();
         $CheckMonth=date("Y-m");		//当前月第一天
         $sub_M=$subMonth-1;
         $StratMonth=date("Y-m",strtotime("$CheckMonth -$sub_M month"));//计算的起始日期
         for($i=0;$i<$subMonth;$i++){
              $tmpMonth=date("Y-m",strtotime(" $StratMonth +$i month"));
              $monthTotal[$tmpMonth]=0;
              $mAmount[$tmpMonth]=0;
			  $mPrechar[$tmpMonth]="";  //
	         echo "<td width='80' class='A1101' align='center'>$tmpMonth</td>";
         }
    ?>
    <td width="80" class="A1101" align="center">已付订金RMB</td>
     <td width="80" class="A1101" align="center">已付订金USD</td>
	<td width="80" class="A1101" align="center">合计RMB</td>
  </tr>
</table>
<?php 
//读取全部未付货款
$AllResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount
FROM $DataIn.cw1_fkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE S.Estate=3 AND Amount>0 ",$link_id));
$AllAmount=sprintf("%.0f",$AllResult["Amount"]);

//读取未付货款
$ShipResult = mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount,S.CompanyId,P.Forshort,P.Letter,P.Id
FROM $DataIn.cw1_fkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE S.Estate=3 AND Amount>0 
GROUP BY S.CompanyId ORDER BY Amount DESC",$link_id);
$TotalAmount=0;$djTotalAmount=0; $NoCdjTotalAmount=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Letter=$ShipRow["Letter"];
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$Letter."-".$ShipRow["Forshort"];
			$Id=$ShipRow["Id"];
			$Idc=anmaIn("providerdata",$SinkOrder,$motherSTR);
			$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
			//加密
			$Forshort="<a style='text-decoration: none; color: #444;' href='../public/companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";		
		$Amount=sprintf("%.0f",$ShipRow["Amount"]);
		
		$MonthResult= mysql_query("SELECT S.Month,SUM(S.Amount*C.Rate) AS Amount ,SUM(S.Amount) AS NoCAmount,C.preChar 
FROM $DataIn.cw1_fkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE S.Estate=3 AND Amount>0 AND S.CompanyId='$CompanyId' GROUP BY S.Month ORDER BY S.Month",$link_id);
		
		

		
	while($MonthRow=mysql_fetch_array($MonthResult)){
		    $Month=$MonthRow["Month"];
			$preChar=$MonthRow["preChar"];
			
			//echo "$preChar";
			if($preChar!="¥") {
				$mAmount[$Month]=$MonthRow["NoCAmount"];
				$mPrechar[$Month]="$preChar";
			}
			else {
		    	$mAmount[$Month]=$MonthRow["Amount"];
				$mPrechar[$Month]="";
			}
			
			//$preChar=$MonthRow["preChar"];
			//$mAmount[$Month]=$MonthRow["Amount"];
		    $monthTotal[$Month]+=$MonthRow["Amount"];
	}
	
	//已付订金
	$checkDjResult=mysql_fetch_array(mysql_query("SELECT SUM(-1*S.Amount*C.Rate) AS Amount ,SUM(-1*S.Amount) AS NoCAmount,C.preChar  
	FROM $DataIn.cw2_fkdjsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
    LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	 WHERE S.CompanyId='$CompanyId' and S.Did='0' and S.Estate=0 ",$link_id));
  	  
	  $djpreChar=$checkDjResult["preChar"];
	  $NoCdjAmount=$checkDjResult["NoCAmount"];
	  $djAmount=$checkDjResult["Amount"];
	  if($djpreChar!="¥") { 
		$NoCdjTotalAmount+= $djAmount;
	  }
	  else {
		  $djpreChar="";
	  }
     
	  
      $djTotalAmount+= $djAmount;
	  $Amount+= $djAmount;
	  //百分比
	  $TempPC=$AllAmount!=0?($Amount/$AllAmount)*100:0;
	  $TempPC=$TempPC>=1?"<span class='greenB'>".(round($TempPC)."%")."</span>":"&nbsp;";
		
	  $TotalAmount+=$Amount;
	 $Amount=number_format($Amount);
	 
?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			 <td class="A0111" width="50" height="25" align="center"><?php  echo $CompanyId?></td>
			 <td class="A0100" width="200" >&nbsp;<?php  echo $showPurchaseorder?>&nbsp;<?php  echo $Forshort?></td>
			 <td class="A0101" width="25" align="right"><?php echo $TempPC ?></td>
			
	<?php
		$DivNum="a";
	   	for($j=0;$j<$subMonth;$j++){
	   
               $tmpMonth=date("Y-m",strtotime("$StratMonth +$j month"));
               $tmpAmount=$mAmount[$tmpMonth]==0?0:number_format($mAmount[$tmpMonth]);
			   //$tmpAmount=number_format($mAmount[$tmpMonth]);
			   $preChar=$mPrechar[$tmpMonth];
               if ($mAmount[$tmpMonth]>0){
	                 $TempId="$CompanyId|$tmpMonth";
	                 $onClickStr="onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_fkcount_b\",\"desktask\");'";
               }
              else{
	              $onClickStr="";
               }
               if ($mAmount[$tmpMonth]>1000000){
	               $tmpAmount="<span style='color:#FF0000;font-weight:bold;'>$tmpAmount</span>";
               }
               else{
	               if ($mAmount[$tmpMonth]>500000){
	                      $tmpAmount="<span style='color:#FF8100'>$tmpAmount</span>";
                    }
               }
			   
			   //echo "<td width='80' class='A0101' align='right' $onClickStr>$tmpAmount</td>"; 
			   
			   ///////////////////////////////////////by zx
               if ( $mAmount[$tmpMonth]==0) {
               	  echo "<td width='80' class='A0101' align='right' $onClickStr>&nbsp;</td>";
			   }
			   else {
				  echo "<td width='80' class='A0101' align='right' $onClickStr> $preChar$tmpAmount</td>"; 
			   }
			   
               $mAmount[$tmpMonth]=0;
			   $mPrechar[$tmpMonth]=0;
               
               $HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
         }
         
         if ( $djAmount==0){
             $djAmount="&nbsp;";
			 $NoCdjAmount="&nbsp;";
              $onClickStr="";
         }
         else{
	         //$djAmount=number_format($checkDjResult["Amount"]);
			  $djAmount=number_format($djAmount);
			  $NoCdjAmount=number_format($NoCdjAmount);
	          $TempId="$CompanyId|$i";
	          $onClickStr="onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_fkcount_dj\",\"desktask\");'";
         }
		 if($djpreChar!="") { 
            echo "<td width='80' class='A0101' align='right' $onClickStr>&nbsp;</td>";
			echo "<td width='80' class='A0101' align='right' $onClickStr>$djpreChar$NoCdjAmount</td>";
		 }
		 else 
		 {
            echo "<td width='80' class='A0101' align='right' $onClickStr>$djAmount</td>";
			echo "<td width='80' class='A0101' align='right' $onClickStr>&nbsp;</td>";
			 
		 }
	?>	
			<td class="A0101" width="80" align="right"><?php  echo $Amount?></td>
		</tr>
	</table>
<?php 
		echo $HideTableHTML;
		$i++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
//读取客户退款
$ShipResult = mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount,S.CompanyId,P.Forshort,P.Letter,P.Id
FROM $DataIn.cw1_tkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE S.Estate=3 
GROUP BY S.CompanyId ORDER BY  Amount",$link_id);
//$Total1=0;$Total2=0;$Total3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	do{
		$Letter=$ShipRow["Letter"];
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$Letter."-".$ShipRow["Forshort"];
			$Id=$ShipRow["Id"];
			$Idc=anmaIn("providerdata",$SinkOrder,$motherSTR);
			$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
			//加密
			$Forshort="<a href='../public/companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";
			
		$Amount=sprintf("%.0f",$ShipRow["Amount"]);
		$TotalAmount+=$Amount;
		$Amount=number_format($Amount);

		$MonthResult= mysql_query("SELECT S.Month,SUM(S.Amount*C.Rate) AS Amount,SUM(S.Amount) AS NoCAmount,C.preChar  
FROM $DataIn.cw1_tkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE S.Estate=3 AND S.CompanyId='$CompanyId' GROUP BY S.Month ORDER BY S.Month",$link_id);

	while($MonthRow=mysql_fetch_array($MonthResult)){
		    $Month=$MonthRow["Month"];
			$preChar=$MonthRow["preChar"];
			if($preChar!="¥") {
				$mAmount[$Month]=$MonthRow["NoCAmount"];
				$mPrechar[$Month]="$preChar";
			}
			else {
		    	$mAmount[$Month]=$MonthRow["Amount"];
				$mPrechar[$Month]="";
			}			
			
		    //$mAmount[$Month]=$MonthRow["Amount"];
		    $monthTotal[$Month]+=$MonthRow["Amount"];
	}
	
?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
		    <td class="A0111" width="50" height="25" align="center"><?php  echo $CompanyId?></td>
			<td class="A0100" width="200" height="25">&nbsp;<?php  echo $showPurchaseorder?>&nbsp;<span class="redB"><?php  echo $Forshort?></span></td>
			<td class="A0101" width="25" >&nbsp;</td>
	<?php
    	$DivNum="a";
	   	for($j=0;$j<$subMonth;$j++){
               $tmpMonth=date("Y-m",strtotime("$StratMonth +$j month"));
               //$tmpAmount=$mAmount[$tmpMonth]==0?"&nbsp;":number_format($mAmount[$tmpMonth]);
			   $tmpAmount=$mAmount[$tmpMonth]==0?0:number_format($mAmount[$tmpMonth]);
			   $preChar=$mPrechar[$tmpMonth];
                if ($mAmount[$tmpMonth]>0){
	                 $TempId="$CompanyId|$tmpMonth";
	                 $onClickStr="onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_fkcount_bt\",\"desktask\");'";
               }
              else{
	              $onClickStr="";
               }
                if ($mAmount[$tmpMonth]>1000000){
	               $tmpAmount="<span style='color:#FF0000;font-weight:bold;'>$tmpAmount</span>";
               }
               else{
	               if ($mAmount[$tmpMonth]>500000){
	                      $tmpAmount="<span style='color:#FF8100'>$tmpAmount</span>";
                    }
               }
			  
               if ( $mAmount[$tmpMonth]==0) {
               	  echo "<td width='80' class='A0101' align='right' $onClickStr>&nbsp;</td>";
			   }
			   else {
				  echo "<td width='80' class='A0101' align='right' $onClickStr> $preChar$tmpAmount</td>"; 
			   }               
			   //echo "<td width='80' class='A0101' align='right' $onClickStr>$tmpAmount</td>";
		
               $mAmount[$tmpMonth]=0;
			   $mPrechar[$tmpMonth]=0;
               
                 $HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
          }
	    ?>
	        <td class="A0101" width="80" align="right">&nbsp;</td>	
            <td class="A0101" width="80" align="right">&nbsp;</td>		
			<td class="A0101" width="80" align="right"><?php  echo $Amount?></td>
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
    <td class="A0100" height="25" width="200" align="center"><b>合 计:</b></td>
    <td class="A0101" width="25" >&nbsp;</td>
    <?php
	   	for($i=0;$i<$subMonth;$i++){
               $tmpMonth=date("Y-m",strtotime("$StratMonth +$i month"));
               $tmpAmount=$monthTotal[$tmpMonth]==0?"&nbsp;":number_format($monthTotal[$tmpMonth]);
               echo "<td width='80' class='A0101' align='right'><b>$tmpAmount</b></td>";
               $monthTotal[$tmpMonth]=0;
          }
	    ?>		
	<td class="A0101" width="80" align="right"><b><?php  echo number_format($djTotalAmount-$NoCdjTotalAmount);?></b></td>
    <td class="A0101" width="80" align="right"> <?php  echo number_format( $NoCdjTotalAmount);?></td>		
	<td class="A0101" width="80" align="right"><b><?php  echo number_format($TotalAmount)?></b></td>
  </tr>
</table>
<?php
//读取已付订金
$i=$i+1;
$djAmount=0;
$ShipResult = mysql_query("SELECT SUM(-1*S.Amount*C.Rate) AS Amount,S.CompanyId,P.Forshort,P.Letter,P.Id  
	FROM $DataIn.cw2_fkdjsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
    LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	 WHERE  1 AND S.Did=0   AND S.Estate=0 
	 AND S.CompanyId NOT IN (SELECT CompanyId FROM $DataIn.cw1_fkoutsheet WHERE Estate=3 GROUP BY CompanyId) 
	 GROUP BY S.CompanyId  ORDER BY Amount",$link_id);
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	do{
		$Letter=$ShipRow["Letter"];
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$Letter."-".$ShipRow["Forshort"];
			$Id=$ShipRow["Id"];
			$Idc=anmaIn("providerdata",$SinkOrder,$motherSTR);
			$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
			//加密
			$Forshort="<a style='text-decoration: none; color: #444;' href='../public/companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";			
		$Amount=sprintf("%.0f",$ShipRow["Amount"]);
		$djAmount+=$Amount;
		$Amount=number_format($Amount);

		$MonthResult= mysql_query("SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Month,SUM(-1*S.Amount*C.Rate) AS Amount,SUM(-1*S.Amount) AS NoCAmount,C.preChar  
FROM $DataIn.cw2_fkdjsheet S 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE  1 AND S.Did='0'  AND S.Estate=0  AND S.CompanyId='$CompanyId' GROUP BY DATE_FORMAT(S.Date,'%Y-%m') ORDER BY Month",$link_id);
	while($MonthRow=mysql_fetch_array($MonthResult)){
		    $Month=$MonthRow["Month"];
		    //$mAmount[$Month]=$MonthRow["Amount"];
			$preChar=$MonthRow["preChar"];
			if($preChar!="¥") {
				$mAmount[$Month]=$MonthRow["NoCAmount"];
				$mPrechar[$Month]="$preChar";
			}
			else {
		    	$mAmount[$Month]=$MonthRow["Amount"];
				$mPrechar[$Month]="";
			}			
		    $monthTotal[$Month]+=$MonthRow["Amount"];
	}
?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			 <td class="A0111" width="50" height="25" align="center"><?php  echo $CompanyId?></td>
			<td class="A0100" width="200" height="25">&nbsp;<span class="greenB"><?php  echo $Forshort?></span></td>
			<td class="A0101" width="25" >&nbsp;</td>
	<?php
    	$DivNum="d";
		
	   	for($j=0;$j<$subMonth;$j++){
               $tmpMonth=date("Y-m",strtotime("$StratMonth +$j month"));
               //$tmpAmount=$mAmount[$tmpMonth]==0?"&nbsp;":number_format($mAmount[$tmpMonth]);
			   $tmpAmount=$mAmount[$tmpMonth]==0?0:number_format($mAmount[$tmpMonth]);
			    $preChar=$mPrechar[$tmpMonth];
                if ($mAmount[$tmpMonth]>1000000){
	               $tmpAmount="<span style='color:#FF0000;font-weight:bold;'>$tmpAmount</span>";
               }
               else{
	               if ($mAmount[$tmpMonth]>500000){
	                      $tmpAmount="<span style='color:#FF8100'>$tmpAmount</span>";
                    }
               }

               //echo "<td width='80' class='A0101' align='right'>$tmpAmount</td>";
                if ( $mAmount[$tmpMonth]==0) {
               	  echo "<td width='80' class='A0101' align='right' >&nbsp;</td>";
			   }
			   else {
				  echo "<td width='80' class='A0101' align='right' > $preChar$tmpAmount</td>"; 
			   }                
			   
			   $mAmount[$tmpMonth]=0;
               
                 $HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
          }
          $TempId="$CompanyId|$i";
	      $onClickStr="onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_fkcount_dj\",\"desktask\");'";
		  
	      echo "<td class='A0101' width='80' align='right' $onClickStr>$Amount</td>	";
	    ?>
	        <td class="A0101" width="80" align="right">&nbsp;</td>	
			<td class="A0101" width="80" align="right">&nbsp;</td>
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
    <td class="A0100" height="25" width="200" align="center"><b>只有已付订金合计:</b></td>
    <td class="A0101" width="25" >&nbsp;</td>
    <?php
	   	for($i=0;$i<$subMonth;$i++){
               $tmpMonth=date("Y-m",strtotime("$StratMonth +$i month"));
               $tmpAmount=$monthTotal[$tmpMonth]==0?"&nbsp;":number_format($monthTotal[$tmpMonth]);
               echo "<td width='80' class='A0101' align='right'><b>$tmpAmount</b></td>";
          }
	    ?>		
	<td class="A0101" width="80" align="right"><b><?php  echo number_format($djAmount);?></b></td>
	<td class="A0101" width="80" align="right">&nbsp;</td>
    <td class="A0101" width="80" align="right">&nbsp;</td>
  </tr>
</table>

<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="50" height="25"class="A0111" align="center">供应商ID</td>
    <td width="200" height="25"class="A0100" align="center">&nbsp;供&nbsp;应&nbsp;商</td>
    <td class="A0101" width="25" >&nbsp;</td>
    <?php 
         $CheckMonth=date("Y-m");		//当前月第一天
        // $StratMonth=date("Y-m",strtotime("$CheckMonth -$sub_M month"));//计算的起始日期
         for($i=0;$i<$subMonth;$i++){
              $tmpMonth=date("Y-m",strtotime(" $StratMonth +$i month"));
	         echo "<td width='80' class='A0101' align='center'>$tmpMonth</td>";
         }
    ?>
    <td width="80" class="A0101" align="center">已付订金RMB</td>
    <td width="80" class="A0101" align="center">已付订金USD</td>
	<td width="80" class="A0101" align="center">合计RMB</td>
  </tr>
</table>
</form>
</body>
</html>

<script language="JavaScript" type="text/JavaScript">
function SandH(divNum,RowId,f,TempId,ToPage,FromDir){
	     var e=eval("HideTable_"+divNum+RowId);
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
