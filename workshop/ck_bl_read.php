<?php
include_once "tasks_function.php";
include "../basic/parameter.inc";

$Floor=$Floor==""?3:$Floor;//3楼备料
$Line= $Line==""?1:$Line;

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
$curWeek=$dateResult["curWeek"];

$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
$nextWeek=$dateResult["NextWeek"];

$curDate=date("Y-m-d");
$today=date("Y-m-d H:i:s");

$ListSTR="";	

$m=0;$ProductArray=array();
$TotalOverQty=0; $OverCount=0;//逾期
$CurWeekQty=0;  $CurWeekCount=0;//本周
$NextWeekQty=0;  $NextWeekCount=0;//下周+	
$AllotQty=0;   $AllotCount=0;   
$WaitBlQty=0;   $WaitBlCount=0; 
$TodayQty=0;//当天已占用
$SortDate=array();$dataArray=array();]
 //可占用
$mySql="SELECT M.CompanyId,M.OrderDate,M.OrderPO,S.POrderId,S.ProductId,S.Qty,C.Forshort,P.cName,P.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks 
FROM $DataIn.yw1_ordermain M
INNER JOIN $DataIn.yw1_ordersheet S  ON M.OrderNumber=S.OrderNumber
INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
WHERE 1 and S.scFrom>0 AND S.Estate=1    AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) >0 AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) <='$nextWeek' 
		AND NOT EXISTS(SELECT E.POrderId FROM $DataIn.yw2_orderexpress E WHERE  E.POrderId=S.POrderId AND E.Type=2) 
		AND NOT EXISTS(SELECT C.POrderId FROM $DataIn.sc1_cjtj C WHERE C.POrderId=S.POrderId) 
GROUP BY S.POrderId ORDER BY Weeks,POrderId";
$myResult=mysql_query($mySql,$link_id);
while($myRow = mysql_fetch_array($myResult)) {
        $POrderId=$myRow["POrderId"];
        $ProductId=$myRow["ProductId"];
       $Qty=$myRow["Qty"];  
       
         $BLSign=0;//1.可备料 2.已占用 
	    //检查订单备料情况
		$CheckblState=mysql_query("
				SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks,SUM(IFNULL(L.llEstate,0)) AS llEstate,Max(L.BlDate) AS BlDate   
				FROM $DataIn.cg1_stocksheet G 
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				INNER JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
				LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
				LEFT JOIN ( 
				    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate,Max(M.Date) AS BlDate  
				    FROM  $DataIn.ck5_llsheet L 
				    LEFT JOIN $DataIn.yw9_blmain M ON L.Pid=M.Id 
				    WHERE  L.POrderId='$POrderId'  GROUP BY L.StockId 
				  )L ON L.StockId=G.StockId 
				WHERE G.POrderId='$POrderId' AND ST.mainType<2 
				AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')",$link_id);

			 if($blStateRow = mysql_fetch_array($CheckblState)){
			      $R_K1=$blStateRow["K1"];
			      $R_K2=$blStateRow["K2"];
				  $R_blQty=$blStateRow["blQty"];
				  $R_llQty=$blStateRow["llQty"];
				  $R_llEstate=$blStateRow["llEstate"];
				  $R_Locks=$blStateRow["Locks"];
				  $R_BlDate=$blStateRow["BlDate"];//占用时间
				  
				    if ($R_blQty==$R_llQty) {
				          $TodayQty+=$curDate==substr($R_BlDate, 0,10)?$Qty:0;
				          $BLSign=$R_llEstate>0?2:0;
				    }
				    else{
					      //是否已存在有可备料订单
						  if (!in_array($ProductId, $ProductArray)) {
						         if ($R_K1>=$R_K2 &&  $R_blQty!=$R_llQty && $R_Locks==0){
							             $ProductArray[]=$ProductId;   $BLSign=1;  
								  }
								  else{
									      $ProductArray[]=$ProductId; 
								  }
						}
				    }
		  }
		  
		  if ($BLSign>0){
		         if ($TestSign=="new" && $BLSign!=$Line) continue;
			      $AbleDateResult=mysql_query("SELECT ableDate  FROM $DataIn.ck_bldatetime WHERE POrderId='$POrderId' ",$link_id);
	             if($AbleDateRow=mysql_fetch_array($AbleDateResult)){
	                     $kbl_Date=$AbleDateRow["ableDate"];
	                     $kbl_Date=$kbl_Date=="0000-00-00 00:00:00"?$today:$kbl_Date;
	            }
	            else{
	                    $kbl_Date=$today;
	            }
	            
	            $lblSTR="";$lblDateSTR="";$LineDateSTR="";  $ScLine="";$kblColor="";
	            $kblTextColor="";$lblTextColor="";$AllotTextColor="";
	           
	            if ($BLSign==1){
	                 $Key="3" . strtotime($kbl_Date);
		             $KblDate=GetDateTimeOutString($kbl_Date,'');
		             $kblColor=" style='background-color: #888888;' ";
		             $kblTextColor=(strtotime($today)-strtotime($kbl_Date))/60>=30?"red_color ":"";
	            }
	            else{
	                $R_BlDate=strtotime($R_BlDate)<strtotime($kbl_Date)?$kbl_Date:$R_BlDate;
		            $KblDate=GetDateTimeOutString($kbl_Date,$R_BlDate);
		            $KblDate=str_replace("前", "&nbsp;&nbsp;&nbsp;", $KblDate);
		            $kblTextColor=(strtotime($R_BlDate)-strtotime($kbl_Date))/60>=30?"red_color ":"";
		            //是否已分配
		            $ScLineResult=mysql_query("SELECT S.DateTime,G.GroupName FROM $DataIn.sc1_mission S
											   LEFT JOIN $DataIn.staffgroup G ON G.Id=S.Operator 
											   WHERE S.POrderId='$POrderId' AND G.Id>0",$link_id);
					if($ScLineRow = mysql_fetch_array($ScLineResult)){
					      $GroupName=$ScLineRow ["GroupName"];
					      $AllotDate=$ScLineRow ["DateTime"];
					      $ScLine="<div>" . substr($GroupName,-1) . "</div>";
					      $WaitBlQty+=$myRow["Qty"];$WaitBlCount++;
					}
					else{
						  $AllotQty+=$myRow["Qty"];$AllotCount++;
					}
					
					if ($ScLine==""){
					      $Key="2" . strtotime($R_BlDate);
						  $lblDate=GetDateTimeOutString($R_BlDate,"");
						  $lblTextColor=(strtotime($today)-strtotime($R_BlDate))/60>=30?" red_color ":"";
						   $lblSTR="<br><span class='$lblTextColor'>$lblDate</span><div style='background-color: #888888;'>配</div>";
					}
					else{
					      $Key="1" . strtotime($AllotDate);
						  $lblDate=GetDateTimeOutString($R_BlDate,$AllotDate);
						  $lblDate=str_replace("前", "&nbsp;&nbsp;&nbsp;", $lblDate);
						  $lblSTR="<br>$lblDate<div>配</div>";
						  $lblTextColor=(strtotime($AllotDate)-strtotime($R_BlDate))/60>=30?" red_color ":"";
						  $AllotTextColor==(strtotime($today)-strtotime($AllotDate))/60>=30?" red_color ":"";
						  //未备料至车间
						  $ablDate=GetDateTimeOutString($AllotDate,"");
						  $LineDateSTR="<span class='$AllotTextColor'>$ablDate</span><div style='background-color: #888888;'>备</div>";
					}
	            }
			  
			  $SortDate[$POrderId]=$Key;
			  
			  $GroupId=$myRow["GroupId"];
			  $POrderId=$myRow["POrderId"];
			  $Week1=substr($myRow["Weeks"], 4,1);
			  $Week2=substr($myRow["Weeks"], 5,1);
			  $Forshort=$myRow["Forshort"];
			  $cName=$myRow["cName"];
			  $OrderPO=$myRow["OrderPO"];
			  $Qty=$myRow["Qty"];
			  
			  if ($curWeek>$myRow["Weeks"]){
				  $WeekColor='bgcolor_red';
				  $TotalOverQty+=$Qty; $OverCount++;
			  }
			  else{
			       $WeekColor='bgcolor_black';
				   if ($curWeek==$myRow["Weeks"]){
					     $CurWeekQty+=$Qty; $CurWeekCount++;
				   }
				   else{
					    $NextWeekQty+=$Qty; $NextWeekCount++;
				   }
			  }
			  
			  $Qty=number_format($Qty);
			  
			  $dataArray[$POrderId]="
				<tr>
				    <td rowspan='2' width='120' class='week $WeekColor'><div>$Week1</div><div>$Week2</div></td>
				     <td colspan='2' width='700' class='title'><span>$Forshort-</span>$cName </td>
				     <td width='260' class='time'>$ScLine</td>
			   </tr>
			   <tr>
				    <td width='350'>$OrderPO</td>
				    <td width='350' class='qty'><img src='image/order.png'/>$Qty</td>
				    <td width='260' class='time'>$LineDateSTR$lblSTR</td>
			   </tr>
			   <tr>
						        <td colspan='3' class='remark'>&nbsp;</td>
						        <td  class='time'><span class='$kblTextColor'>$KblDate</span><div $kblColor>占</div></td>
					      </tr>
			  ";
	    
   }		
}

 $m=0;
asort($SortDate);
while(list($key,$val)= each($SortDate)) {
if ($m<10) {
	 $ListSTR.="<table id='ListTable$m' name='ListTable[]'  border='0' cellpadding='0' cellspacing='0'>";
	 $ListSTR.=$dataArray[$key]." </table>"; 
	 $m++;
	}
}
				   
$TotalQty=$TotalOverQty+$CurWeekQty+$NextWeekQty;
$TotalCount=$OverCount+$CurWeekCount+$NextWeekCount;

$TotalQty=number_format($TotalQty);
$TotalOverQty=number_format($TotalOverQty);
$CurWeekQty=number_format($CurWeekQty);
$NextWeekQty=number_format($NextWeekQty);

if ($TestSign=="new"){
	     $ImageName=$Line==2?"blsign_1.png":"blsign_0.png";
	     $WeekName="<img src='image/$ImageName' style='width:160px;height:160px;margin-bottom:15px;'>";
	     $WeekDiv="linediv";
	     
	  //今日备料
	  if ($Line==1){
		   $blResult=mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS Qty FROM ( 
			          SELECT S.Qty,A.POrderId,SUM(G.OrderQty) AS OrderQty,A.llQty  FROM (
			               SELECT L.POrderId,SUM(IFNULL(L.Qty,0)) AS llQty 
							    FROM  $DataIn.yw9_blmain M 
							    LEFT JOIN $DataIn.ck5_llsheet L ON L.Pid=M.Id 
			                    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=L.StuffId
			                    LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId  
							    WHERE   DATE_FORMAT(M.Date,'%Y-%m-%d')='$curDate'  AND L.POrderId>0 AND T.mainType<2 GROUP BY L.POrderId)A 
					LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
					LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=A.POrderId  
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					WHERE T.mainType<2  GROUP BY A.POrderId 
			)B WHERE B.OrderQty=B.llQty ",$link_id));
	  }
	  else{
			 $blResult=mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS Qty FROM ( 
			          SELECT S.Qty,A.POrderId,SUM(G.OrderQty) AS OrderQty,A.llQty  FROM (
			               SELECT L.POrderId,SUM(IFNULL(L.Qty,0)) AS llQty 
							    FROM  $DataIn.ck5_llmain M 
							    LEFT JOIN $DataIn.ck5_llsheet L ON L.Mid=M.Id 
			                    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=L.StuffId
			                    LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId  
							    WHERE  M.Date='$curDate'  AND L.Estate=0 AND L.POrderId>0 AND T.mainType<2 GROUP BY L.POrderId)A 
					LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
					LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=A.POrderId  
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					WHERE T.mainType<2  GROUP BY A.POrderId 
			)B WHERE B.OrderQty=B.llQty ",$link_id));
	}
    $TodayQty=$blResult["Qty"]==""?0:number_format($blResult["Qty"]);	
}else{
	//今日备料
	$blResult=mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS Qty FROM ( 
	          SELECT S.Qty,A.POrderId,SUM(G.OrderQty) AS OrderQty,A.llQty FROM (
	               SELECT L.POrderId,SUM(IFNULL(L.Qty,0)) AS llQty  
					    FROM  $DataIn.ck5_llmain M 
					    LEFT JOIN $DataIn.ck5_llsheet L ON L.Mid=M.Id 
	                    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=L.StuffId
	                    LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId  
					    WHERE  M.Date='$curDate' AND L.Estate=0 AND L.POrderId>0 AND T.mainType<2 GROUP BY L.POrderId)A 
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=A.POrderId  
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE T.mainType<2  GROUP BY A.POrderId 
	)B WHERE B.OrderQty=B.llQty ",$link_id));
	$TodayQty=$blResult["Qty"]==""?0:number_format($blResult["Qty"]);	
	$WeekName=substr($curWeek, 4,2);
	$WeekDiv="weekdiv";	
}

 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
 
  $nextWeek=substr($nextWeek, 4,2);

?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
   <div id='<?php echo $WeekDiv; ?>' class='float_left'><?php echo $WeekName; ?></div>
   <ul id='quantity3' class='float_right' style='padding-top:50px;'>
             <li class='text_left'><?php echo $TotalQty; ?></li>
             <li style='width:24px;'><div></div></li>
	         <li class='text_right'><span><?php echo $TodayQty; ?></span></li>
   </ul>
 <?php if ($TestSign=="new" && $Line==2){?>
  <ul id='count'>
          <li style='width: 540px;'><div></div>待备料<br><span><?php echo $WaitBlQty; ?></span><span><?php echo  "($WaitBlCount)"; ?> </span></li>
	      <li style='width: 540px;'>待分配 <br><span><?php echo $AllotQty; ?> </span><span><?php echo  "($AllotCount)"; ?> </span></li>
	 </ul>
<?php }else{ ?>
   <ul id='count' class='border3'>
           <li>逾期 <div></div><br><span class='red_color'><?php echo $TotalOverQty; ?> </span><span><?php echo  "($OverCount)"; ?> </span></li>
	       <li>本周 <div></div><br><span><?php echo $CurWeekQty; ?></span><span><?php echo "($CurWeekCount)"; ?></span></li>
	      <li><?php echo $nextWeek; ?>周+ <br><span><?php echo $NextWeekQty; ?></span><span><?php echo "($NextWeekCount)"; ?></span></li>
	 </ul>
<?php } ?>
</div>
<div id='listdiv' style='overflow: hidden;height:1690px;width:1080px;'>
<?php echo $ListSTR;?>
</div>