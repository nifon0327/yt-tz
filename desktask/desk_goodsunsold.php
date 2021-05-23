<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 未出订单货款统计");
$tableWidth=1160;
$subTableWidth=1100;
//$subMonth=7;//统计月份数量
$i=1;
//未出货订单总额
$noshipResult = mysql_query("SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 and S.Estate>'0'",$link_id);
if($noshipRow = mysql_fetch_array($noshipResult)) {
	$AllOrderAmount=sprintf("%.0f",$noshipRow["Amount"]);
	}
$noProfitResult = mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>'0'",$link_id);
if($noProfitRow = mysql_fetch_array($noProfitResult)) {
	$AllProfitAmount=sprintf("%.0f",($AllOrderAmount-$noProfitRow["oTheCost"]));
	}

$MResult=mysql_query("SELECT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month
          FROM $DataIn.yw1_ordermain M
          LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber
          WHERE S.Estate>0  GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m') ORDER BY DATE_FORMAT(M.OrderDate,'%Y-%m')",$link_id);
while($MRow=mysql_fetch_array($MResult)){
       $Marray[]=$MRow["Month"];
}
?>
<body>
<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="25" >未出订单货款统计</td>
    </tr>
	<tr>
   	 <td align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="90" height="25"class="A1110" align="center">&nbsp;客&nbsp;&nbsp;户</td>
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
	<td width="80" class="A1101" align="center">小计(USD)</td>
	<td width="80" class="A1101" align="center">小计(RMB)</td>
	<td width="80" class="A1101" align="center">毛利率</td>
  </tr>
</table>
<?php 
//读取未收货款
$ShipResult = mysql_query("SELECT Amount,CompanyId,Forshort,Currency,Rate FROM (
	SELECT SUM(S.Price*S.Qty) AS Amount,M.CompanyId,C.Forshort,C.Currency,D.Rate,SUM(S.Price*S.Qty*D.Rate) AS OrderByAnoumt
	FROM $DataIn.yw1_ordermain M
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
	WHERE S.Estate!='0'  GROUP BY M.CompanyId ORDER BY C.OrderBy DESC
	) A ORDER BY OrderByAnoumt DESC",$link_id);
$Total=0;$Total_1=0;$Total_2=0;$Total_3=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		  $FK_1=0;$FK_2=0;$FK_3=0;$FK_JY=0;
		  $CompanyId=$ShipRow["CompanyId"];
		  $Forshort=$ShipRow["Forshort"];
		  $Rate=$ShipRow["Rate"];
		  $Currency=$ShipRow["Currency"];
          if($Currency==1)$symbol="￥";
          else $symbol="";
          $TempFKSTR="FK_".strval($Currency);
          $$TempFKSTR=sprintf("%.2f",$ShipRow["Amount"]);
		  $TempFKSUM="Total_".strval($Currency);
          $$TempFKSUM=$$TempFKSUM+$$TempFKSTR;
		  $TempRMB=$$TempFKSTR*$Rate;
		  $Total=$Total+$TempRMB;
		  $TempPC=($TempRMB/$AllOrderAmount)*100;
		  $TempPC=$TempPC>=1?"<span class='greenB' titile='订单比例'>".(round($TempPC)."%")."</span>":"";
		//计算毛利
	    $Result = mysql_fetch_array(mysql_query("	
		SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount,A.oTheCost
		FROM $DataIn.yw1_ordermain M
		LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
		LEFT JOIN (
			SELECT SUM((A.AddQty+A.FactualQty)*A.Price*C.Rate) AS oTheCost,M.CompanyId
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			WHERE 1 AND S.Estate>'0' GROUP BY M.CompanyId
			) A ON A.CompanyId=M.CompanyId
		WHERE S.Estate>0  AND M.CompanyId='$CompanyId' GROUP BY  M.CompanyId",$link_id));//GROUP BY  M.CompanyId 低版本不可省略
	    $cbAmount=sprintf("%.0f",$Result["oTheCost"]);//成本
	    $ddbl=sprintf("%.1f",($TempRMB/$AllOrderAmount)*100);//=订单金额/总订单金额
	    $TempProfit=sprintf("%.0f",$TempRMB-$cbAmount);	    //毛利
	    $mlbl=sprintf("%.1f",($TempProfit/$AllProfitAmount)*100);//=毛利/总毛利
	    $ProfitTempPC=sprintf("%.0f",($TempProfit/$TempRMB)*100);	    //毛利率
	    $ProfitTotal=$ProfitTotal+$TempProfit;	   //毛利总额
		
		 $MonthResult= mysql_query("SELECT SUM(S.Price*S.Qty) AS Amount,DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month,D.Rate
          FROM $DataIn.yw1_ordermain M
          LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber = M.OrderNumber
          LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
          LEFT JOIN $DataPublic.currencydata D ON C.Currency=D.Id	
          WHERE S.Estate>0 AND M.CompanyId='$CompanyId' GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')",$link_id);
	      while($MonthRow=mysql_fetch_array($MonthResult)){
		            $Month=$MonthRow["Month"];
                    $MonthRate=$MonthRow["Rate"];	        
		            $mAmount[$Month]=$MonthRow["Amount"];
		            $monthTotal[$Month]+=$MonthRow["Amount"]*$MonthRate;
	           }

?>
	<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php  echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0110" width="90" height="25">&nbsp;<?php  echo $showPurchaseorder?>&nbsp;<?php  echo $Forshort?></td>
            <td class="A0101" width="25" align="right"><?php echo $TempPC ?></td>
	<?php
		$DivNum="a";
	   	for($j=0;$j<$subMonth;$j++){	   
	           $tmpMonth=$Marray[$j];
               //$tmpMonth=date("Y-m",strtotime("$StratMonth +$j month"));
               $tmpAmount=$mAmount[$tmpMonth]==0?"&nbsp;":$symbol.number_format(sprintf("%.2f",$mAmount[$tmpMonth]),2);                 
	           $TempId="$CompanyId|$tmpMonth|$DivNum$i";
	           $onClickStr="onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_goodsunsold_a\",\"desktask\");'";
             
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
			<td class="A0101" width="80" align="right"><?php echo $FK_2?></td><!-- USD-->
			<td class="A0101" width="80" align="right"><?php echo $FK_1?></td><!-- RMB-->
			<td class="A0101" width="80" align="right"><?php echo $ProfitTempPC ?>%</td><!-- 预收货款-->
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
    <td class="A0110" height="25" width="90">合 计:</td>
    <td class="A0101" width="25" >&nbsp;</td>
    <?php
	   	for($i=0;$i<$subMonth;$i++){
              // $tmpMonth=date("Y-m",strtotime("$StratMonth +$i month"));
               $tmpMonth=$Marray[$i];
               $tmpAmount=$monthTotal[$tmpMonth]==0?"&nbsp;":number_format(sprintf("%.2f",$monthTotal[$tmpMonth]),2);
               echo "<td width='80' class='A0101' align='right'>￥".$tmpAmount."</td>";
          }
          $Total_2=$Total_2==0?"&nbsp;":number_format($Total_2,2);
          $Total_1=$Total_1==0?"&nbsp;":number_format($Total_1,2);
	    ?>		
	<td class="A0101" width="80" align="right"><?php  echo $Total_2?></td><!-- USD-->
	<td class="A0101" width="80" align="right"><?php  echo $Total_1?></td><!-- RMB-->
	<td class="A0101" width="80" align="right">&nbsp;</td><!--毛利率-->
  </tr>
<tr  height="25" class=''><td class="A0111" colspan="11">转RMB总额约：<?php    echo zerotospace(number_format(sprintf("%.2f",$Total)))."元"?></td></tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="90" height="25"class="A0110" align="center">&nbsp;客&nbsp;&nbsp;户</td>
    <td class="A0101" width="25" >&nbsp;</td>
    <?php 
         $CheckMonth=date("Y-m");		//当前月第一天
        // $StratMonth=date("Y-m",strtotime("$CheckMonth -$sub_M month"));//计算的起始日期
         for($i=0;$i<$subMonth;$i++){
              $tmpMonth=date("Y-m",strtotime(" $StratMonth +$i month"));
	         echo "<td width='80' class='A0101' align='center'>$tmpMonth</td>";
         }
    ?>
	<td width="80" class="A0101" align="center">小计(USD)</td>
	<td width="80" class="A0101" align="center">小计(RMB)</td>
	<td width="80" class="A0101" align="center">毛利率</td>
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
