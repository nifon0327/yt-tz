<?php
     //$Line=$Line==""?"C":$Line;
      include_once "tasks_function.php";
      include "../basic/parameter.inc";
      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
   
     $SC_TYPE='7100';
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     
      $myResult = mysql_query(" SELECT A.GroupId,A.POrderId,A.boxId,MAX(A.Date) AS ScDate,SUM(A.Qty) AS ScQty,S.Qty,S.sgRemark,S.scFrom,S.Estate,M.OrderPO,C.Forshort,P.cName,P.InspectionSign,
      YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks  
    FROM  $DataIn.yw1_ordersheet S   
    LEFT JOIN   $DataIn.sc1_cjtj A ON S.POrderId=A.POrderId  
	LEFT JOIN   $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN   $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
	LEFT JOIN   $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
	LEFT JOIN   $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
	LEFT JOIN   $DataIn.productdata P ON P.ProductId=S.ProductId
    WHERE  S.Estate=1  AND A.TypeId='$SC_TYPE' GROUP BY A.POrderId 
	ORDER BY S.scFrom DESC,ScDate " ,$link_id); 
	
$SumScQty=0;$ScCount=0;
$OweQty=0;$OweCount=0;
$SumOweQty=0; 
$ListSTR="";	$m=0;	$TotalCount=0;	$FinishCount=0;
while($myRow = mysql_fetch_assoc($myResult)){
      $GroupId=$myRow["GroupId"];
	  $POrderId=$myRow["POrderId"];
	  $Week1=substr($myRow["Weeks"], 4,1);
	  $Week2=substr($myRow["Weeks"], 5,1);
	  $WeekColor=$curWeek>$myRow["Weeks"]?'bgcolor_red':'bgcolor_black';
	  $Forshort=$myRow["Forshort"];
	  $cName=$myRow["cName"];
	  $OrderPO=$myRow["OrderPO"];
	  $Qty=$myRow["Qty"];
	  $ScQty=$myRow["ScQty"];
	 $scFrom=$myRow["scFrom"];
	  $Estate=$myRow["Estate"];
	  $ScDate=$myRow["ScDate"];
	  $InspectionSign=$myRow["InspectionSign"];
	  
	 $djClass="";$tableClass="";
	 $ScQtyRow=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='$SC_TYPE'",$link_id));
	   $ScQty2=$ScQtyRow["Qty"];
	   //已完成
	   $Inspection=0;
	   if ($ScQty2==$Qty && $Estate==1){
		     $SumScQty+=$ScQty;
	         $ScCount++;
	          $tableClass="table_image";
	          if ($InspectionSign==1){
		            $checkInspectResult=mysql_query("SELECT I.Inspection FROM $DataIn.yw1_productinspection I WHERE I.POrderId='$POrderId' ORDER BY I.Id DESC LIMIT 1",$link_id);
		            if ($InspectRow = mysql_fetch_assoc($checkInspectResult)){
		                 $Inspection=$InspectRow["Inspection"];
			              if ($Inspection==1)$FinishCount++;
		            }
	          }
	          else{
		          $FinishCount++;
	          }
	   }				
	  else{
			 //欠尾数	          
			 
			  if ((strtotime($today)-strtotime($ScDate))/60>30 && $Qty>$ScQty2){
				  $OweQty+=$Qty-$ScQty2;
				  $SumOweQty+=$Qty;
				  $OweCount++;
				  $tableClass="tb_bgcolor1";
			  }
			  else{
			      continue;
			 }
	 }
	  
	  $ScLine=substr($myRow["boxId"], 0,1);
	  
	  
	  //$PackRemark=str_replace("(拆分的订单)", "", $myRow["PackRemark"]);
	  $sgRemark=$myRow["sgRemark"];
	  $DateStr=GetDateTimeOutString($ScDate,'');
	  $ScHours=(strtotime($today)-strtotime($ScDate))/60;
	  $ScColors=$ScHours/60>24 || ($ScHours>30 && $ScQty2<>$Qty)?"red_color":"";
	  
	  $Qty=number_format($Qty);
	  $ScQty2=number_format($ScQty2);
	  if ($m<10){
	            if ($InspectionSign==1){
		             $InspectionImage="<img src='image/inspection_$Inspection.png' style='float:right;margin-right:10px;'/>";
	            }
	            else{
		            $InspectionImage="";
	            }
			  $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass'>
				<tr>
				    <td rowspan='2' width='120' class='week $WeekColor'><div>$Week1</div><div>$Week2</div></td>
				    <td colspan='4' width='960' class='title'><span>$Forshort-</span>$cName</td>
			   </tr>
			   <tr>
				    <td width='350'>$OrderPO</td>
				    <td width='250' class='qty'><img src='image/order.png'/>$Qty</td>
				    <td width='480' class='qty $djClass' colspan='2'><img src='image/register.png'/>$ScQty2 $InspectionImage</td>
			   </tr>";
			   // <td width='280' class='td_line'><div class='line'>$ScLine</div></td>//
			   if ($sgRemark!=""){
				   $ListSTR.="<tr>
								        <td  class='remark_icon'  style='height: 60px;'><img src='image/remark.png'/></td>
								        <td colspan='3' class='remark'  style='height: 60px;'>$sgRemark</td>
								        <td  width='280' class='time time2 $ScColors' >$DateStr<div style='margin-bottom:5px;'>$ScLine</div></td>
							      </tr>";
			   }
			   else{
				   $ListSTR.="<tr>
								        <td  class='remark_icon'  style='height: 60px;'>&nbsp;</td>
								        <td colspan='3' class='remark'  style='height: 60px;'>&nbsp;</td>
								        <td  width='280'  class='time  time2 $ScColors'>$DateStr<div style='margin-bottom:5px;'>$ScLine</div></td>
							      </tr>";
			   }
			 $ListSTR.="</table>";
	 }
	 $m++;
}

$TotalCount=$ScCount+$OweCount;

$SumOweQty=number_format($SumOweQty);
$OweQty="<span class='red_color'>" . number_format($OweQty) . "</span>/$SumOweQty<span style='color:#888888'>($OweCount)</span>";
$SumScQty="<span class='green_color'>" . number_format($SumScQty) . "</span><span style='color:#888888'>($ScCount)</span>";

//今日生产数量
$ScedResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS ScQty    
		FROM $DataIn.sc1_cjtj S 
		WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND S.TypeId='$SC_TYPE' ",$link_id));
$TotalScQty=$ScedResult["ScQty"]==""?0:number_format($ScedResult["ScQty"]);

//本月生产数量
$curMonth=date("Y-m");
$ScedResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS ScQty    
		FROM $DataIn.sc1_cjtj S 
		WHERE  DATE_FORMAT(S.Date,'%Y-%m')='$curMonth' AND S.TypeId='$SC_TYPE' ",$link_id));
$MonthScQty=$ScedResult["ScQty"]==""?0:number_format($ScedResult["ScQty"]);

//上班人员统计
include "staff_counts.php";

	
//10天平均生产数量
/*
$countSign=1;
$content = file_get_contents('taskscount.data');
if ($content){
	$dataArray=json_decode($content,true);
	$oldDate=$dataArray['date'];
	if ($oldDate==$today){
		 $countArray=$dataArray;
		 $countSign=0;
	}
}

if ($countSign==1) $countArray['date']=$today;

if ($countSign==1){
	        $yDate=date("Y-m-d",strtotime("-1 day"));
			$k=0;$n=0;$DateCheckRows="";
			do{
			   $eDate=date("Y-m-d",strtotime("$yDate  -$n   day"));
			   //判断当天是否有登记生产数量
			   $CheckScState=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj  WHERE DATE_FORMAT(Date,'%Y-%m-%d')='$eDate'",$link_id));
			    if ($CheckScState["Qty"]>0){
					   $k++;
				 }
				$n++;
			}while($k<5 && $n<31);
			$DateCheckRows=" AND DATE_FORMAT(S.Date,'%Y-%m-%d')>='$eDate' AND DATE_FORMAT(S.Date,'%Y-%m-%d')<='$yDate' ";
			 $scResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty FROM $DataIn.sc1_cjtj S WHERE 1 $DateCheckRows AND S.TypeId='7100'",$link_id));
			$avg_Qty=round($scResult["Qty"]/5);
			$countArray['PackAvgQty']=$avg_Qty;
	}
	else{
		$avg_Qty=$countArray['PackAvgQty'];
	}
    $avg_Qty=number_format($avg_Qty);
 */   
     $WeekName=substr($curWeek, 4,2);
     include "../iphoneAPI/subprogram/worktime_read.php";
     $upTime=date("H:i:s");
    // $FinishCount=rand(0,1);
?>
<input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 <input type='hidden' id='FinishCount' name='FinishCount' value='<?php echo $FinishCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
   <div id='weekdiv' class='float_left'><?php echo $WeekName; ?></div>
   <ul id='group' class='float_right'>
	      <li><img src='image/group_staff.png'/><?php echo $GroupNums; ?>人</li>
	      <li><img src='image/working_staff.png'/><?php echo $kqNums; ?>人</li>
	      <li><img src='image/leave_staff.png' style='margin-top:3px;'/><?php echo $LeaveNums; ?>人</li>
	 </ul>
    <ul id='quantity3'   class='float_right' style='width:750px;'>
            <li class='text_left' style='font-size:58pt;'><span style='color:#007C2D'><?php echo $MonthScQty; ?></span></li>
           <li style='width:24px;'><div></div></li>
           <li class='text_right' style='font-size:58pt;'><span><?php echo $TotalScQty; ?></span></li>
   </ul>
<!--
   <ul id='quantity' class='float_right clear_right'>
             <li class='text_right'  style='width:360px;font-size:58pt;'><span><?php echo $TotalScQty;  ?></span><div></div></li>
	         <li class='text_left' style='width:400px;font-size:58pt;'><?php echo $MonthScQty; ?></li>
   </ul>
   -->
	 <ul id='count'>
	      <li style='width: 540px;'><div></div>欠尾数 <br><span><?php echo $OweQty; ?> </span></li>
	      <li style='width: 540px;'>待入库<br><span><?php echo $SumScQty; ?></span></li>
	 </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1492px;width:1080px;'>
<?php echo $ListSTR;?>
</div>

<?php 
/*
if ($countSign==1){
		$fp = fopen("taskscount.data", "w");
		$txt=json_encode($countArray);
		fwrite($fp,$txt );
		fclose($fp); 
}
*/
?>