<?php   
//电信-zxq 2012-08-01

include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
$Year=$Year==""?"0":$Year;
if($Year=="0"){
	$Celnum=18;
 } 
else{
   $Celnum=17;	
}
$CelWidth=60;
//$divWidth=$CelWidth*14;
$CelSumWidth=$CelWidth*($Celnum-2)+130;
$RowHeight=25;
ChangeWtitle("$SubCompany 供应商交期分析");
$i=1;
//border-color:#CCC;
?>

<style type="text/css">
<!-- 
.table { 
width:<?php    echo $CelSumWidth+$Celnum+1?>px!important; 
width:<?php    echo $CelSumWidth?>px;
margin-left:auto;
table-layout: fixed;
margin-right:auto;
text-align:center;
background-color:#E0E0E0;
} 
--> 
</style> 
<body>
<center> 
<form action="" method="get" name="form1">
	<P style='text-align:center;font-size:28px;font-weight:bold;'><?php    echo $TempY?>供应商交货日期分析</p>
    <p style='text-align:center;'> <select name="Year" id="Year" onChange="javascript:document.form1.submit()">
	  <?php   
	  if($Year=="0"){
			echo"<option value='0' selected>最近13个月</option>";
			$ListYearStr="最近13个月";
			}
	  else{
			echo"<option value='0'>最近13个月</option>";
	   }	
	  $checkY=mysql_query("SELECT DATE_FORMAT(Date,'%Y')  AS Date FROM $DataIn.cg1_stockmain GROUP BY DATE_FORMAT(Date,'%Y') ORDER BY Date DESC",$link_id);
	  if($checkR=mysql_fetch_array($checkY)){
	  	do{
			$theYaer=$checkR["Date"];
			if($Year==$theYaer){
				echo"<option value='$theYaer' selected>$theYaer 年</option>";
				}
			else{
				echo"<option value='$theYaer'>$theYaer 年</option>";
				}
			}while ($checkR=mysql_fetch_array($checkY));
		}
	  ?>
      </select></p>
    <?php   
//按供应商分类统计数据
$curDate=date("Y-m-d");
if ($Year=="0"){
	 $sYear=date("Y")-1;
	 $Month=date("m");
	 $SearchRows="DATE_FORMAT(M.Date,'%Y-%m')>='$sYear-$Month'";
	$ToMonth=13;
	$startDate=date("Y") ."-". $Month . "-01";
	$ckFlag="DESC";
    }
else{
	 $SearchRows="DATE_FORMAT(M.Date,'%Y')=$Year";
	 $ToMonth=12;
	 $startDate=$Year . "-12-01";
	 $ckFlag="ASC";
    }
 
 //2011-4-5以后才有填交货日期的时间数据
 $SearchFilter="AND M.Date<'2011-4-5'";
 
 //日期数组初始化
for($i=0;$i<$ToMonth;$i++){
	$sDate=date("Y-m",strtotime("$startDate  -$i   month"));
	$DateArray[$sDate]=$sDate;
//	$DateList1[$sDate]=0;$DateList2[$sDate]=0;$DateList3[$sDate]=0;$DateListT[$i]=0;
}
if ($ToMonth<13){
   asort($DateArray);//排序
}
	?>
 	<Table class='table' border=1>
  		<tr height=28px style='background-color:#B3E9FD;'>
          <td width=30px>序号</td>
          <td width=100px>供应商名称</td>
          <td width=60px>订单总数</td> 
          <td width=60px>确认周期</td>  
     <?php   
	$ListArray=$DateArray;
	while (list($Keys,$mDate) = each($ListArray))
    {
	 echo "<td width=60px>".$mDate."</td>";
	}
	?>
        <td width=60px>全年合计</td>
	  </tr>
      </Table>
  <?php   
 $sumResult = mysql_query("
	  	SELECT S.CompanyId,C.Forshort,COUNT(*) AS cgSum FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.MId 											
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	    WHERE $SearchRows AND C.Estate='1' AND S.CompanyId<>'2166' 
		GROUP BY S.CompanyId ORDER BY cgSum DESC 
		",$link_id);
 $n=1;
 if($sumResult_Row=mysql_fetch_array($sumResult)){
  do{	
     $CompanyId=$sumResult_Row["CompanyId"];
     $CompanyName=$sumResult_Row["Forshort"];
	 $cgSum=$sumResult_Row["cgSum"];
     $bgType="style='background-color:#FFF;'";
	if ($n%2==0) $bgType="style='background-color:#B3E9FD;'";
	 echo "<Table class='table' border=1>";
	 echo"<tr height=25px $bgType>";
	 echo "<td rowspan='4' width=30px>$n</td>";
	 echo "<td rowspan='4' width=100px>$CompanyName</td>";
	 echo "<td rowspan='4' width=60px>$cgSum</td>"; 
	 
//初始化统计数据
   for($i=0;$i<$ToMonth;$i++){
	$sDate=date("Y-m",strtotime("$startDate  -$i   month"));
	$DateList1[$sDate]=0;$DateList2[$sDate]=0;$DateList3[$sDate]=0;$DateList4[$sDate]=0;
	$DateList5[$sDate]=0;$DateTotal[$sDate]=0;$DateListT[$i]=0;
} 
	 //三天内已确认订单
	 $mySql="SELECT COUNT(*) AS cgSum1, U.Month 
      FROM (
         SELECT D.StockId,DATE_FORMAT(D.Date,'%Y-%m')AS Month  										
	      FROM (
             SELECT S.StockId,M.Date 
		     FROM $DataIn.cg1_stocksheet S 
             LEFT JOIN $DataIn.cg1_stockmain M  ON M.Id = S.MId
             WHERE  $SearchRows 
             AND datediff( S.DeliveryDate,M.Date )<=3 AND S.CompanyId = '$CompanyId' 
          UNION ALL 
		    SELECT S.StockId,M.Date  
            FROM $DataIn.cg1_stocksheet S  
			LEFT JOIN  $DataIn.cg1_stockmain M ON M.Id = S.MId 
            LEFT JOIN $DataIn.deliverydate Y ON S.StockId = Y.StockId 
            WHERE  $SearchRows AND datediff(Y.DateTime,M.Date)<=3  AND S.CompanyId = '$CompanyId' GROUP BY S.StockId 
		   )D GROUP BY  D.StockId 
     )U  GROUP BY U.Month  ORDER BY U.Month $ckFlag";
	  $CheckSql=mysql_query($mySql,$link_id);  
	  if($CheckRow=mysql_fetch_array($CheckSql)){
	     $sumCount=0;
	   do{
		  $Month=$CheckRow["Month"];
		  $cgSum1=$CheckRow["cgSum1"];
		  $DateList1[$Month]=$cgSum1;
		  $sumCount+=$cgSum1;
		 }while($CheckRow=mysql_fetch_array($CheckSql));
	    $DateListT[1]=$sumCount; 
	  }
	  
	 //未填交期订单
		$CheckSql=mysql_query("
	  	SELECT COUNT(*) AS cgSum3,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
		FROM $DataIn.cg1_stocksheet S  
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.MId 
	    WHERE $SearchRows AND S.DeliveryDate='0000-00-00' AND S.CompanyId='$CompanyId'  GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date $ckFlag
		",$link_id);   
   	  if($CheckRow=mysql_fetch_array($CheckSql)){
	     $sumCount=0;
	   do{
		  $Month=$CheckRow["Month"];
		  $cgSum3=$CheckRow["cgSum3"];
		  $DateList3[$Month]=$cgSum3;
		  $sumCount+=$cgSum3;
		 }while($CheckRow=mysql_fetch_array($CheckSql));
	    $DateListT[3]=$sumCount; 
	  }  
	  
	 ///超三天已确认订单=订单总数-前面两项
		$CheckSql=mysql_query("
	  	SELECT COUNT(*) AS cgSumAll,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
		FROM $DataIn.cg1_stocksheet S  
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.MId 
	    WHERE $SearchRows AND S.CompanyId='$CompanyId'  GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date $ckFlag
		",$link_id);   
   	  if($CheckRow=mysql_fetch_array($CheckSql)){
	     $sumCount=0;
	   do{
		  $Month=$CheckRow["Month"];
		  $cgSumAll=$CheckRow["cgSumAll"];
		  $DateTotal[$Month]=$cgSumAll;
		  $cgSum2=$cgSumAll-$DateList1[$Month]-$DateList3[$Month];
		  $DateList2[$Month]=$cgSum2;
		  $sumCount+=$cgSum2;
		 }while($CheckRow=mysql_fetch_array($CheckSql));
	    $DateListT[2]=$sumCount; 
	  }

 	 //交期变更频率
		$CheckSql=mysql_query("
	  	SELECT COUNT(*) AS cgSum4,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
		             FROM $DataIn.deliverydate D 
					 LEFT JOIN $DataIn.cg1_stocksheet  S ON S.StockId=D.StockId 
					 LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					 WHERE $SearchRows AND S.CompanyId='$CompanyId'
					 GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date $ckFlag
		",$link_id);   
   	  if($CheckRow=mysql_fetch_array($CheckSql)){
	     $sumCount=0;
	   do{
		  $Month=$CheckRow["Month"];
		  $cgSum4=$CheckRow["cgSum4"];
		  $DateList4[$Month]=$cgSum4;
		  $sumCount+=$cgSum4;
		 }while($CheckRow=mysql_fetch_array($CheckSql));
	    $DateListT[4]=$sumCount; 
	  }
	  
	$CheckSql=mysql_query("
	 SELECT COUNT(*) AS cgSum5,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
			         FROM (SELECT M.Date,D.StockId FROM  $DataIn.deliverydate D 
					 LEFT JOIN $DataIn.cg1_stocksheet  S ON S.StockId=D.StockId 
					 LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					 WHERE $SearchRows AND S.CompanyId='$CompanyId' GROUP BY D.StockId)A
					 GROUP BY DATE_FORMAT(A.Date,'%Y-%m')ORDER BY A.Date $ckFlag
		",$link_id);   
	if ($CheckSql){
   	  if($CheckRow=mysql_fetch_array($CheckSql)){
	     $sumCount=0;
	   do{
		  $Month=$CheckRow["Month"];
		  $cgSum5=$CheckRow["cgSum5"];
		  $DateList5[$Month]=$cgSum5;
		  $sumCount+=$cgSum5;
		 }while($CheckRow=mysql_fetch_array($CheckSql));
	    $DateListT[5]=$sumCount; 
	  }
	}
	  	  
  $wth="width='" . $CelWidth . "px' align='center'";
  
  //统计每月订单数
 /*   $ListArray=$DateArray;
	while (list($Keys,$mDate) = each($ListArray))
    {
	  $DateTotal[$mDate]=$DateList1[$mDate]+$DateList2[$mDate]+$DateList3[$mDate];	
	}
*/  
    echo "<td $wth>3 天 内</td>"; //显示3日内订单
  $ListArray=$DateArray;
	while (list($Keys,$mDate) = each($ListArray))
    {
		if ($DateList1[$mDate]>0){
		   
		  $AV_Sum1=number_format($DateList1[$mDate]/$DateTotal[$mDate],4)*100;	//占订单总数百分比
		  echo "<td $wth><font style='color:#00F;'>$AV_Sum1%</font></br>($DateList1[$mDate])</td>";  
		}
		else{
		echo "<td $wth>-</td>";  	
		}
	}
	   $AV_Sum1=number_format($DateListT[1]/$cgSum,4)*100;	//占订单总数百分比
	   echo "<td $wth><font style='color:#00F;'>$AV_Sum1%</font></br>($DateListT[1])</td>"; 
	   echo "</tr>"; 
	
  echo "<tr style='background-color:#BBFDBB;'> <td $wth>超过3天</td>"; 
  $ListArray=$DateArray;
	while (list($Keys,$mDate) = each($ListArray))
    {
		if ($DateList2[$mDate]>0){
		  $AV_Sum2=number_format($DateList2[$mDate]/$DateTotal[$mDate],4)*100;	//占订单总数百分比
		  echo "<td $wth><font style='color:#F00;'>$AV_Sum2%</font></br>($DateList2[$mDate])</td>";  
		}
		else{
		echo "<td $wth>-</td>";  	
		}
	}
	   $AV_Sum2=number_format($DateListT[2]/$cgSum,4)*100;	//占订单总数百分比
	   echo "<td $wth><font style='color:#F00;'>$AV_Sum2%</font></br>($DateListT[2])</td>"; 
	   echo "</tr>"; 
	   
 echo "<tr style='background-color:#E8E8E8;'> <td $wth>未填交期</td>"; 
  $ListArray=$DateArray;
	while (list($Keys,$mDate) = each($ListArray))
    {
		if ($DateList3[$mDate]>0){
		  $AV_Sum3=number_format($DateList3[$mDate]/$DateTotal[$mDate],4)*100;	//占订单总数百分比
		  echo "<td $wth><font style='color:#00F;'>$AV_Sum3%</font></br>($DateList3[$mDate])</td>";  
		}
		else{
		echo "<td $wth>-</td>";  	
		}
	}
	   $AV_Sum3=number_format($DateListT[3]/$cgSum,4)*100;	//占订单总数百分比
	   echo "<td $wth><font style='color:#00F;'>$AV_Sum3%</font></br>($DateListT[3])</td>"; 
	   echo "</tr>";
	   
   echo "<tr height=22px style='background-color:#FCC;'> <td $wth>变更频率</td>"; 
  $ListArray=$DateArray;
	while (list($Keys,$mDate) = each($ListArray))
    {
		$tempList5=$DateList5[$mDate]-$DateList4[$mDate];
		if ($tempList5>0 && $DateList5[$mDate]!=0){
		  $AV_Sum4=number_format($tempList5/$DateList5[$mDate],4)*100;	//占订单总数百分比
		  echo "<td $wth><font style='color:#F00;'>$AV_Sum4%</font></td>";  
		}
		else{
		echo "<td $wth>-</td>";  	
		}
	}
	if ($DateListT[5]!=0)
	   $AV_Sum4=number_format(($DateListT[5]-$DateListT[4])/$DateListT[5],4)*100;	//占订单总数百分比
	 else
	    $AV_Sum4=0;
	   echo "<td $wth><font style='color:#F00;'>$AV_Sum4%</font></td>"; 
	   echo"</tr></table>";	
 /*  
	  $cg_Sum2=$cgResult["cgSum2"];
	  $AV_Sum2=number_format($cg_Sum2/$cgSum,4)*100;	//占订单总数百分比
	 echo "<li style='width:$CelWidth" . "px;$rowType'><font style='color:#F00;'>$AV_Sum2%</font>($cg_Sum2)</li>"; 

	 //未填交期订单
		$cgResult=mysql_fetch_array(mysql_query("
	  	SELECT COUNT(*) AS cgSum6 FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.MId 
		LEFT JOIN  (
			SELECT StockId,Min(DateTime) as min_Date FROM $DataIn.deliverydate 
			GROUP BY StockId
			) D ON D.StockId=S.StockId 
	    WHERE $SearchRows AND  S.DeliveryDate='0000-00-00' AND S.CompanyId='$CompanyId' 
		",$link_id));   
   
	  $cg_Sum6=$cgResult["cgSum6"];
	  $AV_Sum6=number_format($cg_Sum6/$cgSum,4)*100;	//占订单总数百分比
	 echo "<li style='width:$CelWidth" . "px;$rowType'><font style='color:#F00;'>$AV_Sum6%</font>($cg_Sum6)</li>"; 
	 
	//按时交货单   
	 $cgResult=mysql_fetch_array(mysql_query("
		SELECT COUNT(*) AS cgSum3 FROM (SELECT COUNT(*) FROM $DataIn.cg1_stocksheet S 
					 LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					 LEFT JOIN $DataIn.ck1_rksheet C ON C.StockId=S.StockId 
                     LEFT JOIN $DataIn.ck1_rkmain K ON K.Id=C.Mid  
					 WHERE $SearchRows AND S.CompanyId='$CompanyId' AND 
					 datediff(K.Date,S.DeliveryDate)<=0 GROUP BY S.StockId) A
					 ",$link_id));  
	 
	  $cg_Sum3=$cgResult["cgSum3"];
	  $AV_Sum3=number_format($cg_Sum3/$cgSum,4)*100;	//占订单总数百分比
	  echo "<li style='width:$CelWidth" . "px;$rowType'><font style='color:#00F;'>$AV_Sum3%</font>($cg_Sum3)</li>";   
	  
	 //未按时交货单,包含未填交期已收货单
		 $cgResult=mysql_fetch_array(mysql_query(" 
		SELECT COUNT(*) AS cgSum4 FROM (SELECT COUNT(*) FROM $DataIn.cg1_stocksheet S 
					 LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					 LEFT JOIN $DataIn.ck1_rksheet C ON C.StockId=S.StockId 
                     LEFT JOIN $DataIn.ck1_rkmain K ON K.Id=C.Mid  
					 WHERE $SearchRows AND S.CompanyId='$CompanyId' AND 
					 (datediff(K.Date,S.DeliveryDate)>0 OR S.DeliveryDate='0000-00-00') 
					 GROUP BY S.StockId)A
					 ",$link_id));  
	 
	  $cg_Sum4=$cgResult["cgSum4"];
	  $AV_Sum4=number_format($cg_Sum4/$cgSum,4)*100;	//占订单总数百分比
	  echo "<li style='width:$CelWidth" . "px;$rowType'><font style='color:#F00;'>$AV_Sum4%</font>($cg_Sum4)</li>";    
	
	//未交货单
	  $cg_Sum7=$cgSum-$cg_Sum3-$cg_Sum4;
	  if ($cg_Sum7>0){  
	    $AV_Sum7=number_format($cg_Sum7/$cgSum,4)*100;	//占订单总数百分比
	    echo "<li style='width:$CelWidth" . "px;$rowType'><font style='color:#F00;'>$AV_Sum7%</font>($cg_Sum7)</li>";      } 
	 else{
		 echo "<li style='width:$CelWidth" . "px;$rowType'>-</li>";  
	  }
	  
	*/  
  $n++; 
   }while($sumResult_Row=mysql_fetch_array($sumResult));  
 }
?>
</form>
</center>
</br>
<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>说明：</b>因2011年4月5日前系统未记录供应商每一次的交期变更记录，因此之前日期不统计。&nbsp;计算公式：交期变更频率=交期变更次数/变更订单数。
</span>
</body>
</html>