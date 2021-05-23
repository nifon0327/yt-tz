<?php   
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=1110;

//取得美金汇率
$checkUsdRate=mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Symbol='USD' LIMIT 1",$link_id));
$usdRate=$checkUsdRate["Rate"];
?>
<style>
a.clientP{color:#333;text-decoration:none; }
a.clientP:hover {color:#CC3300;text-decoration:none;}

#fixed_div{
position: absolute;
left: 8px;
top: 106px;
}


#HideDivright{ 
    left:2110px; 
    height:1px;
    width: 1400px;
   position:relative;
}  

</style>
<body>
<form name="form1" method="post" action="">
<div  id="main">
<DIV id="HideDivleft">
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"   style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center" >
		<td height="24" colspan="5">客户每月下单出货统计</td>
    </tr>

	<tr>
		<td height="24">
			<select name="ChooseMonth" id="ChooseMonth"  style="width: 100px;"  onchange='document.form1.submit();'>
				<!--<option value="">最近6个月</option>-->
				<?php   
				$ChooseMonth=$ChooseMonth==""?date("Y"):$ChooseMonth;
				 for ($m=date("Y");$m>=2008;$m--){
				     if ($ChooseMonth==$m){
					      echo "<option value='$m' selected>$m" . "</option>";
					 }
				 else{
					 echo "<option value='$m'>$m" . "</option>";
					}
				 }
				?>
			</select>
		</td>
   	 <td height="24" colspan="4" align="right">统计日期:<?php    echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="150" class="A1110" height="25">&nbsp;</td>
            <td class="A1101" width="40">&nbsp;</td>
<?php   
$StartY=date("Y");
$MonthTemp=date("m")+1;
switch($ChooseMonth){
   // case 0:
        // $monthAmount=6;
        //break;
    default:
        $StartY=$ChooseMonth+1;
        $MonthTemp=1;
        $monthAmount=12;
       break;    
}
$startMonth=$ChooseMonth."-01";
$endMonth=$ChooseMonth."-12";
//$endMonth=substr(date("Y-m-d"),0,7);
//$monthAmount=6;
$MonthArray=array();
$tdWidth=isSafari6()==1?183:180;
$tableTitle="";
//$tempyear=$StartY-1;
for($i=1;$i<=$monthAmount;$i++){
	            $MonthTemp--;
	            $MonthTemp=$MonthTemp<1?12:$MonthTemp;
	            if($MonthTemp%12==0 && $ChooseMonth!=0)$StartY=$StartY-1;
              //  if($ChooseMonth==0 && $MonthTemp>6)$StartY=$tempyear;
	            $tableTitle.="<td width='$tdWidth'  class='A1101'  align='center' colspan='2'>$StartY"."年".$MonthTemp."月"."</td>";
                $MonthTemp=$MonthTemp<10?"0".$MonthTemp:$MonthTemp;
                $MonthArray[$i]=$StartY."-".$MonthTemp;
                $MonthInOrder[$i]=0;
                $MonthOutOrder[$i]=0;
                $MonthInQty[$i]=0;
                $MonthOutQty[$i]=0;
             }
      $tableTitle.="<td width='$tdWidth'  class='A1101'  align='center' colspan='2'>合  计</td>"; 
     echo $tableTitle;
$totalResult=mysql_query("SELECT SUM(S.Price*S.Qty*C.Rate) AS Amount,M.CompanyId
                           FROM $DataIn.yw1_ordermain M
                           LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
		                   LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId  AND D.ObjectSign IN (1,2)
		                   LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
                           WHERE 1  and   DATE_FORMAT(M.OrderDate,'%Y-%m')  Between   '$startMonth' AND   '$endMonth'    
          GROUP BY M.CompanyId",$link_id);
//$totalArray=array();
$alltotal=0;
while($totalRow=mysql_fetch_array($totalResult)){
       $totalAmount=$totalRow["Amount"];
       $tempCompanyId=$totalRow["CompanyId"];
        $totalArray[]=array(0=>$tempCompanyId,1=>$totalAmount);
      $alltotal+=$totalAmount;
}
$totalcount=count($totalArray);
?>
  </tr>

</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr>
			<td class="A0110" width="150" height="25" align="center">客户(货币)</td>
            <td class="A0101" width="40">&nbsp;</td>
<?php   
for($i=1;$i<=$monthAmount;$i++){
		echo"<td class='A0101' width='90' align='center'>下单</td>
			      <td class='A0101' width='90' align='center' bgcolor='#C2D4E4'>出货</td>";
               }
        echo"<td class='A0101' width='90' align='center' bgcolor='F3D6CF'>下单</td>
			      <td class='A0101' width='90' align='center' bgcolor='93D6A4'>出货</td>";
?>
		</tr>
	</table>
<?php   //即显示下单的客户，也显示出货的客户
$DivStr="<DIV id='fixed_div'>";
$ShipResult = mysql_query("SELECT * FROM (
       SELECT SUM(Amount) AS Amount,CompanyId,Forshort,Currency,Rate,Symbol,ColorCode,SUM(OrderByAnoumt) AS OrderByAnoumt  FROM (
	      SELECT SUM(S.Price*S.Qty) AS Amount,M.CompanyId,C.Forshort,C.Currency,D.Symbol,D.Rate,
          SUM(S.Price*S.Qty*S.YandN*M.Sign*D.Rate) AS OrderByAnoumt,CH.ColorCode
	      FROM $DataIn.ch1_shipmain M
          LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id
	      LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId  AND C.ObjectSign IN (1,2)
	      LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
          LEFT JOIN $DataIn.chart2_color CH ON CH.CompanyId=C.CompanyId
	      WHERE 1 and M.Estate=0 and   DATE_FORMAT(M.Date,'%Y-%m')  Between  '$startMonth' AND  '$endMonth'  
          GROUP BY M.CompanyId
       UNION ALL
          SELECT '0' AS Amount,M.CompanyId,C.Forshort,C.Currency,D.Symbol,D.Rate,'0'  AS OrderByAnoumt,CH.ColorCode
	      FROM $DataIn.yw1_ordermain M
          LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
	      LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId  AND C.ObjectSign IN (1,2)
	      LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
          LEFT JOIN $DataIn.chart2_color CH ON CH.CompanyId=C.CompanyId
          WHERE 1  and   DATE_FORMAT(M.OrderDate,'%Y-%m')  Between   '$startMonth' AND   '$endMonth'     
          GROUP BY M.CompanyId
	    ) A WHERE 1   GROUP BY A.CompanyId ORDER BY A.OrderByAnoumt DESC
   ) B   ORDER BY B.OrderByAnoumt DESC",$link_id);
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$j=1;$alltotalDgAmount=0;
	do{
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$ShipRow["Forshort"];
        $Currency=$ShipRow["Currency"];
        $Rate=$ShipRow["Rate"];
        $Symbol=$ShipRow["Symbol"];
        $OrderByAnoumt=$ShipRow["OrderByAnoumt"];
        $ColorCode=$ShipRow["ColorCode"]==""?"":"#".$ShipRow["ColorCode"];
        // echo $ColorCode;
        $inOrderTotal=0;$OutOrderTotal=0;
        $InQtyTotal=0;$OutQtyTotal=0;
       //****************************代购产品所站比例
       $DgArray=array();$k=0;
       $totalDgAmount=0;
        $DGResult=mysql_query("SELECT SUM((G.AddQty+G.FactualQty)*G.Price*C.Rate) AS DgAmount , DATE_FORMAT(SM.Date,'%Y-%m') AS DgMonth
         FROM $DataIn.ch1_shipsheet S
        LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=S.Mid
        LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
        LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid
        LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
       LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
       WHERE 1 AND P.ProviderType=1 AND SM.CompanyId=$CompanyId and   DATE_FORMAT(SM.Date,'%Y-%m')  Between  '$startMonth' AND  '$endMonth'  GROUP BY         DATE_FORMAT(SM.Date,'%Y-%m') ORDER BY DATE_FORMAT(SM.Date,'%Y-%m') ",$link_id);
      while($DgRow=mysql_fetch_array($DGResult)){
                  $DgAmount=sprintf("%.0f",$DgRow["DgAmount"]);
                  $DgMonth=$DgRow["DgMonth"];
                  $DgArray[$DgMonth]=$DgAmount;
                  $k++;$totalDgAmount+=$DgAmount;
                }
          $alltotalDgAmount+=$totalDgAmount;
           if($totalDgAmount>0){
                 $DgRate=(sprintf("%.2f",($totalDgAmount)/$OrderByAnoumt)*100)."%";
              }
         else{
                  $DgRate="";
              }
        $clientRate="";
        for($k=0;$k<=$totalcount;$k++){
                if($CompanyId==$totalArray[$k][0]){
                       $clientRate=sprintf("%.2f",$totalArray[$k][1]/$alltotal);
                        break;
                     }
                }
          if($clientRate>0)$clientRate=($clientRate*100)."%";
          else $clientRate="";
      //********************************************
        $DivStr.="<table width='150' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr ><td class='A0110' width='150' height='50' align='center'  bgcolor='$ColorCode'><span style='color: #FFFFFF;font-weight:bold;float:left' title='客户下单所占比例'>$clientRate</span><a  class='clientP' href='Charttopng_clientship.php?CompanyId=$CompanyId&Forshort=$Forshort' target='_blank'>$Forshort"."(".$Symbol.")"."</a></td><td class='A0101' width='40' align='right' title='客户代购占出货的比例' bgcolor='$ColorCode'><span class='purpleN' >".$DgRate."</span></td></tr></table>";



		echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		        <tr ><td class='A0110' width='150' height='50' align='center'  bgcolor='$ColorCode'><span style='color: #FFFFFF;font-weight:bold;float:left'>$clientRate</span><a  class='clientP' href='Charttopng_clientship.php?CompanyId=$CompanyId&Forshort=$Forshort' target='_blank'>$Forshort"."(".$Symbol.")"."</a></td><td class='A0101' width='40' align='right' title='客户代购占出货的比例'><span class='purpleN' >".$DgRate."</span></td>";
        for($i=1;$i<=$monthAmount;$i++){
                //下单出货金额
                $InResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*C.Rate) AS Amount,SUM(S.Qty) AS InQty
                           FROM $DataIn.yw1_ordermain M
                           LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
		                   LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		                   LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
                           WHERE M.CompanyId='$CompanyId' AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$MonthArray[$i]'",$link_id));
                 $inOrderNum=$InResult["Amount"]==0?"&nbsp;":sprintf("%.0f",$InResult["Amount"]);
                 $InQty=$InResult["InQty"];
                 $MonthInOrder[$i]+=sprintf("%.0f",$inOrderNum);
                 $inOrderTotal+=$inOrderNum;
                 $InQtyTotal+=$InQty;   
                 $MonthInQty[$i]+=$InQty ;   
                 $OutResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*S.YandN*M.Sign*C.Rate) AS Amount,SUM(S.Qty) AS OutQty
                           FROM $DataIn.ch1_shipmain M
                           LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id
		                   LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		                   LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
                           WHERE M.CompanyId='$CompanyId' and M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$MonthArray[$i]'",$link_id));
               $OutOrderNum=$OutResult["Amount"]==0?"&nbsp;":sprintf("%.0f",$OutResult["Amount"]);
               $OutQty=$OutResult["OutQty"];
               $MonthOutOrder[$i]+=sprintf("%.0f",$OutOrderNum);
               $OutOrderTotal+=$OutOrderNum;    
               $OutQtyTotal+=$OutQty;
               $MonthOutQty[$i]+=$OutQty;
               if($DgArray[$MonthArray[$i]]>0 && $OutOrderNum>0){
                     $DgMonthRate="(".(sprintf("%.2f",$DgArray[$MonthArray[$i]]/$OutOrderNum)*100)."%)";
                    }
               else $DgMonthRate="";
               $inOrderNum=$inOrderNum=="&nbsp;"?0:number_format($inOrderNum,0);
               $OutOrderNum=$OutOrderNum=="&nbsp;"?0:number_format($OutOrderNum, 0);

		       echo"<td class='A0101' width='90' align='right'>$inOrderNum<br><span class='yellowB'>".number_format($InQty,0)."</span></td>
			            <td class='A0101' width='90' align='right' bgcolor='#C2D4E4'>$OutOrderNum<br><span class='purpleN'>$DgMonthRate".number_format($DgArray[$MonthArray[$i]],0)."</span><br><span class='greenB'>".number_format($OutQty,0)."</span></td>";
               }
               $inOrderTotal=number_format($inOrderTotal,0);
               $OutOrderTotal=number_format($OutOrderTotal, 0);
              echo"<td class='A0101' width='90' align='right' bgcolor='F3D6CF'>$inOrderTotal<br><span class='yellowB'>".number_format($InQtyTotal,0)."</span></td>
			            <td class='A0101' width='90' align='right' bgcolor='93D6A4'>$OutOrderTotal<br><span class='purpleN'>".number_format($totalDgAmount,0)."</span><br><span class='greenB'>".number_format($OutQtyTotal,0)."</span></td>"; 
        echo"</tr></table>";
		$j++;
		}while ($ShipRow = mysql_fetch_array($ShipResult));
	}
$DivStr.="</DIV>";
echo $DivStr;
      //总计(USD)
      $MonthInOrderTotal=0;$MonthOutOrderTotal=0;
      echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		          <tr ><td class='A0110' width='150' height='25' align='center' ><b>总计(USD)</b></td><td class='A0101' width='40'>&nbsp;</td>";
                 for($i=1;$i<=$monthAmount;$i++){
                     $MonthInOrderTotal+=$MonthInOrder[$i];
                     $MonthOutOrderTotal+=$MonthOutOrder[$i];
                     $InOrder=number_format($MonthInOrder[$i]/$usdRate,0);
                     $OutOrder=number_format($MonthOutOrder[$i]/$usdRate, 0);
                     echo"<td class='A0101' width='90' align='right'><b>$InOrder</b></td>
			            <td class='A0101' width='90' align='right' bgcolor='#C2D4E4'><b>$OutOrder</b></td>";
                  }
                     $MonthInOrderTotal=number_format($MonthInOrderTotal/$usdRate,0);
                     $MonthOutOrderTotal=number_format($MonthOutOrderTotal/$usdRate, 0); 
                  echo"<td class='A0101' width='90' align='right' bgcolor='F3D6CF'><b>$MonthInOrderTotal</b></td>
			            <td class='A0101' width='90' align='right'  bgcolor='93D6A4'><b>$MonthOutOrderTotal</b></td>";
			    echo "</tr></table>";
    //总计(RMB)
      $MonthInOrderTotal=0;$MonthOutOrderTotal=0;$MonthInQtyTotal=0;$MonthOutQtyTotal=0;
      echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		          <tr ><td class='A0110' width='150' height='25' align='center' ><b>总计(RMB)</b></td><td class='A0101' width='40'>&nbsp;</td>";
                 for($i=1;$i<=$monthAmount;$i++){
                     $MonthInOrderTotal+=$MonthInOrder[$i];
                     $MonthOutOrderTotal+=$MonthOutOrder[$i];
                     $MonthInQtyTotal+=$MonthInQty[$i];
                     $MonthOutQtyTotal+=$MonthOutQty[$i];
                     $MonthInOrder[$i]=number_format($MonthInOrder[$i],0);
                     $MonthOutOrder[$i]=number_format($MonthOutOrder[$i],0);
                     $MonthInQty[$i]=number_format($MonthInQty[$i],0);
                     $MonthOutQty[$i]=number_format($MonthOutQty[$i],0);
                     echo"<td class='A0101' width='90' align='right'><b>$MonthInOrder[$i]<br><span class='yellowB'>$MonthInQty[$i]</span></b></td>
			            <td class='A0101' width='90' align='right' bgcolor='#C2D4E4'><b>$MonthOutOrder[$i]<br><span class='greenB'>$MonthOutQty[$i]</span></b></td>";
                  }
                     $MonthInOrderTotal=number_format($MonthInOrderTotal,0);
                     $MonthOutOrderTotal=number_format($MonthOutOrderTotal,0); 
                     $MonthInQtyTotal=number_format($MonthInQtyTotal,0); 
                     $MonthOutQtyTotal=number_format($MonthOutQtyTotal,0); 
           echo"<td class='A0101' width='90' align='right' bgcolor='F3D6CF'><b>$MonthInOrderTotal<br><span class='yellowB'>$MonthInQtyTotal</span></b></td>
	        <td class='A0101' width='90' align='right'  bgcolor='93D6A4'><b>$MonthOutOrderTotal<br><span class='greenB'>$MonthOutQtyTotal</span></b></td>";
			    echo "</tr></table>";
?>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr>
			<td class="A0110" width="150" height="25" align="center">客户(货币)</td>
            <td class="A0101" width="40">&nbsp;</td>
<?php   
for($i=1;$i<=$monthAmount;$i++){
		echo"<td class='A0101' width='90' align='center'>下单</td>
			      <td class='A0101' width='90' align='center' bgcolor='#C2D4E4'>出货</td>";
               }
          echo"<td class='A0101' width='90' align='center' bgcolor='F3D6CF'>下单</td>
			      <td class='A0101' width='90' align='center' bgcolor='93D6A4'>出货</td>";
?>
		</tr>
	</table>
<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class=''>
    <td width="150" class="A1110" height="25">&nbsp;</td>
            <td class="A1101" width="40">&nbsp;</td>
<?php    echo $tableTitle;?>
  </tr>
</table>
</DIV>
<DIV id="HideDivright">&nbsp;</DIV>
</div>
</form>
</body>
</html>
<script>
window.onscroll = function(){ 
  //  var t = document.documentElement.scrollLeft || document.body.scrollLeft;  
    var fixed_div=document.getElementById("fixed_div");
            //var move_left=getLeft(fixed_div)+ parseInt(t);
          fixed_div.style.left=document.body.scrollLeft+8;
}

 
//获取元素的纵坐标 
function getTop(e){ 
var offset=e.offsetTop; 
if(e.offsetParent!=null) offset+=getTop(e.offsetParent); 
return offset; 
} 
//获取元素的横坐标 
function getLeft(e){ 
var offset=e.offsetLeft; 
if(e.offsetParent!=null) offset+=getLeft(e.offsetParent); 
return offset; 
} 

</script>
