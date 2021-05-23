<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";

      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $SC_TYPE=7100;//组装加工类型
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";	
   
 //已备料数量
$myResult=mysql_query("SELECT M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.sgRemark,C.Forshort,P.cName,P.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks,A.llEstate,A.BlDate,SC.DateTime,G.GroupName,IF(SC.Id>0,1,0) AS LineSign,S.ShipType    
				 FROM ( 
				     SELECT S1.* FROM (
				          SELECT S0.POrderId,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty,SUM(S0.llEstate) AS llEstate,Max(S0.BlDate) AS BlDate FROM (      
				             SELECT 
										S.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(SUM(L.Estate),0) AS llEstate,Max(BM.Date) AS BlDate 
				                        FROM $DataIn.yw1_ordermain M
										INNER  JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
										INNER  JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				                        INNER  JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										INNER  JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
				                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
                                        LEFT JOIN $DataIn.ck5_llmain LM ON LM.Id=L.Mid
                                        LEFT JOIN $DataIn.yw9_blmain BM ON BM.Id=L.Pid
                                         LEFT JOIN $DataIn.stuffproperty T  ON T.StuffId=G.StuffId AND  T.Property='8'
				                        WHERE  S.scFrom>0 AND S.Estate=1 AND ST.mainType<2 AND T.StuffId IS NULL 
				                        AND NOT EXISTS(SELECT POrderId FROM $DataIn.sc1_cjtj C WHERE C.POrderId=S.POrderId AND TypeId='$SC_TYPE') 
				                        GROUP BY G.StockId 
				               )S0 GROUP BY S0.POrderId 
				     )S1 WHERE S1.blQty=S1.llQty AND S1.llEstate>0 
				)A  
				LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId  
				INNER JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
				INNER JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
				LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
				LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId
				LEFT JOIN $DataIn.sc1_mission SC ON SC.POrderId=S.POrderId
				LEFT JOIN $DataIn.staffgroup G ON G.Id=SC.Operator 
				WHERE 1  ORDER BY LineSign,Weeks,llEstate,BlDate ",$link_id);		

//按周以PI交期分组读取未出订单
$TotalOverQty=0; $OverCount=0;//逾期
$CurWeekQty=0;  $CurWeekCount=0;//本周
$NextWeekQty=0;  $NextWeekCount=0;//下周+
$SumQty=0;
$m=0;
while($myRow = mysql_fetch_array($myResult)) {
	     $Weeks=$myRow["Weeks"];
	     $Qty=$myRow["Qty"];
	     
	      if ($Weeks<$curWeek){
		        $TotalOverQty+=$Qty;$OverCount++;
	      }
	     else{
		        if ($Weeks==$curWeek){
		              $CurWeekQty+=$Qty;$CurWeekCount++;
		        }
		        else{
			         $NextWeekQty+=$Qty;$NextWeekCount++;
		        }
	     }
         
         if ($m<10){
	     $Week1=substr($Weeks, 4,1);
	     $Week2=substr($Weeks, 5,1);
	     $WeekColor=$curWeek>$Weeks?'bgcolor_red':'bgcolor_black';
	     $tdColor=$curWeek>$Weeks?'red_color':'black_color';

		  $Forshort=$myRow["Forshort"];
		  $cName=$myRow["cName"];
		  $OrderPO=$myRow["OrderPO"];
	      $sgRemark=$myRow["sgRemark"];
	    
	     $ScLine=$myRow["GroupName"]==""?"":substr($myRow["GroupName"],-1);
	     $ScLine=$ScLine==""?"":"<span class='time'><div style='float:right;margin:10px 10px 0px 0px;'>$ScLine</div></span>";
	     $TeststandardSign=$myRow["TestStandard"]!=1?"color:#FFFFFF;background-color:#FF0000;":"";
	     //$TeststandardSign=$myRow["TestStandard"]!=1?"border: 3px  solid #FF0000;":"";
	    // $ScLine=$myRow["TestStandard"]!=1?"<img src='image/wait.png' style='float:right;margin:10px 10px 0px 0px;width:48px;height:48px;'/>":$ScLine;
	     
	      $POrderId=$myRow["POrderId"];
	     $checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
		if($checkteststandardRow = mysql_fetch_array($checkteststandard)){	
		      $TeststandardSign="color:#FFFFFF;background-color:#FF0000;";
			//$ScLine="<img src='image/wait.png' style='float:right;margin:10px 10px 0px 0px;width:48px;height:48px;'/>";
		}
	     //$ScLine=$myRow["TestStandard"]!=1?"<div style='background-color: #888888;margin-bottom:12px;'>图</div>":$ScLine;
	     
	   
	      $AbleDateResult=mysql_query("SELECT ableDate  FROM $DataIn.ck_bldatetime WHERE POrderId='$POrderId' ",$link_id);
         if($AbleDateRow=mysql_fetch_array($AbleDateResult)){
                 $kbl_Date=$AbleDateRow["ableDate"];
                 $kbl_Date=$kbl_Date=="0000-00-00 00:00:00"?$today:$kbl_Date;
         }
        else{
                $kbl_Date=$today;
         }
         
         $lblSTR="";$lblDateSTR="";$LineDateSTR=""; $LineChars="";
         $kblColor=""; $kblTextColor="";$lblTextColor="";$AllotTextColor="";
          
	     $BlDate=$myRow["BlDate"];
	     
	     $R_BlDate=strtotime($BlDate)<strtotime($kbl_Date)?$kbl_Date:$BlDate;
         $KblDate=GetDateTimeOutString($kbl_Date,$R_BlDate);
         $KblDate=str_replace("前", "&nbsp;&nbsp;&nbsp;", $KblDate);
         $kblTextColor=(strtotime($R_BlDate)-strtotime($kbl_Date))/60>=30?"red_color ":"";
		            
	     $AllotDate=$myRow ["DateTime"];
	     if ($AllotDate==""){ //未分配
		      $SumQty+=$Qty;
		      $TeststandardSign=$TeststandardSign==""?"background-color: #888888;":$TeststandardSign;
		      $lblDate=GetDateTimeOutString($R_BlDate,"");
			  $lblTextColor=(strtotime($today)-strtotime($R_BlDate))/60>=30?" red_color ":"";
			  $lblSTR="<br><span class='$lblTextColor'>$lblDate</span>";
			  $lblChars="<br><div style='$TeststandardSign'>配</div>";
			  
	     }
	     else{
		      $lblDate=GetDateTimeOutString($R_BlDate,$AllotDate);
			  $lblDate=str_replace("前", "&nbsp;&nbsp;&nbsp;", $lblDate);
			  $lblTextColor=(strtotime($AllotDate)-strtotime($R_BlDate))/60>=30?" red_color ":"";
			  $lblSTR="<br><span class='$lblTextColor'>$lblDate</span>";
			  $lblChars="<br><div style='$TeststandardSign'>配</div>";
			 
			  $AllotTextColor=(strtotime($today)-strtotime($AllotDate))/60>=30?" red_color ":"";
			  //未备料至车间
			  $ablDate=GetDateTimeOutString($AllotDate,"");
			  $LineDateSTR="<span class='$AllotTextColor'>$ablDate</span>";
			  $LineChars="<div style='background-color: #888888;margin-bottom:12px;'>备</div>";
	     }
	     
	      $Qty=number_format($Qty);
	      //出货方式
		  $ShipType=$myRow["ShipType"];
		  $ShipType=$ShipType===""?"":"<image src='../images/ship$ShipType.png' style='float:right;margin:10px 10px 0px 0px;width:48px;height:48px;'/>";
	      
	      $ListSTR.="<table id='ListTable$m' name='ListTable[]'  border='0' cellpadding='0' cellspacing='0'>
			<tr>
			    <td rowspan='2' width='120' class='week $WeekColor'><div>$Week1</div><div>$Week2</div></td>
			    <td colspan='4' width='912' class='title'><span>$Forshort-</span>$cName $ScLine</td>
			     <td width='48'>$ShipType</td>
		   </tr>
		   <tr>
			    <td width='240'>$OrderPO</td>
			    <td width='120' class='qty'>&nbsp;</td>
			    <td width='320' class='qty'><img src='image/order.png'/>$Qty</td>			    
			    <td width='232' class='time' style='padding-right:0;'>$LineDateSTR$lblSTR</td>
			    <td width='48' class='time'>$LineChars$lblChars</td>
		   </tr>";//<td width='220' class='qty'><img src='image/register.png'/>&nbsp;</td>
		   $ListSTR.="<tr>
						         <td colspan='4' style='height:40px;line-height:40px;'>&nbsp;</td>
						         <td width='232' class='time $kblTextColor' style='padding-right: 0;'>$KblDate</td>
			                     <td width='48' class='time'><div>占</div></td>
						   </tr>";
						  
		  $ListSTR.="</table>";
		  
	     /*
	     $SumQty+=$myRow["DateTime"]==""?0:$Qty;
		 $LineChars=$myRow["DateTime"]==""?"":"<div>配</div>";
		 $LineDateStr=$myRow["DateTime"]==""?"":GetDateTimeOutString($myRow["DateTime"],'');
		 $LineColors=(strtotime($today)-strtotime($myRow["DateTime"]))/60>30?"red_color":"";					
	     $BlDate=$myRow["BlDate"];
	     $DateChars="占";
	    
	     $DateStr=GetDateTimeOutString($BlDate,$myRow["DateTime"]);
	     $DateStr=$myRow["DateTime"]==""?$DateStr: str_replace("前", "&nbsp;&nbsp;&nbsp;", $DateStr);
	     $blHours=$myRow["DateTime"]==""?(strtotime($today)-strtotime($BlDate))/3600:(strtotime($myRow["DateTime"])-strtotime($BlDate))/3600;
	     $blColors=$blHours>24?"red_color":"";
	     //$blColors=(strtotime($today)-strtotime($BlDate))/60>30 &&  $myRow["DateTime"]==""?"red_color":"";
	
		 $ListSTR.="<table id='ListTable$m' name='ListTable[]'>
			<tr>
			    <td rowspan='2' width='120' class='week $WeekColor'><div>$Week1</div><div>$Week2</div></td>
			    <td colspan='4' width='912' class='title'><span>$Forshort-</span>$cName</td>
			    <td width='48' class='time'>$ScLine</td>
		   </tr>
		   <tr>
			    <td width='240'>$OrderPO</td>
			    <td width='220' class='qty'><img src='image/order.png'/>$Qty</td>
			    <td width='220' class='qty'><img src='image/register.png'/>&nbsp;</td>
			    <td width='232' class='time $LineColors'>$LineDateStr</td>
			    <td width='48' class='time'>$LineChars</td>
		   </tr>";
		   $ListSTR.="<tr>
						         <td colspan='4' style='height:40px;line-height:40px;'>&nbsp;</td>
						         <td width='232' class='time $blColors'>$DateStr</td>
			                     <td width='48' class='time'><div>$DateChars</div></td>
						         <td class='time'></td>
						   </tr>";
						  
		  $ListSTR.="</table>";
		  */
		 
		 $m++;
	 }
	     
}
				   
//上班人员统计
include "staff_counts.php";

$TotalQty=$TotalOverQty+$CurWeekQty+$NextWeekQty;
$TotalCount=$OverCount+$CurWeekCount+$NextWeekCount;
$SumQty=number_format($SumQty);

$TotalQty=number_format($TotalQty);
$TotalOverQty=number_format($TotalOverQty);
$CurWeekQty=number_format($CurWeekQty);
$NextWeekQty=number_format($NextWeekQty);

$WeekName=substr($curWeek, 4,2);

 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
 
 $LeaderNumber=10200;
 $LeaderName="谢雪梅";
 
 $nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
  $nextWeek=$dateResult["NextWeek"];
  $nextWeek=substr($nextWeek, 4,2);
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
   <div id='weekdiv' class='float_left'><?php echo $WeekName; ?></div>
   <img id='leader'  class='float_left margin_left_15'  src='photo/<?php echo $LeaderNumber; ?>.png'/>  
    <div  id='leader_name'  class='leader_left' ><span><?php echo $LeaderName; ?></span></div>
    <ul id='group' class='float_right'>
	      <li><img src='image/group_staff.png'/><?php echo $GroupNums; ?>人</li>
	      <li><img src='image/working_staff.png'/><?php echo $kqNums; ?>人</li>
	      <li><img src='image/leave_staff.png' style='margin-top:3px;'/><?php echo $LeaveNums; ?>人</li>
	 </ul>
	 <ul id='quantity'   class='float_right' style='width:620px;margin-right:-32px;'>
           <li class='text_right'><span><?php echo $SumQty; ?>&nbsp;</span></li>
           <li style='width:24px;'><div></div></li>
	       <li class='text_left' ><?php echo $TotalQty; ?></li>
   </ul>
   <ul id='count' class='border3'>
           <li>逾期 <div></div><br><span class='red_color'><?php echo $TotalOverQty; ?> </span><span><?php echo  "($OverCount)"; ?> </span></li>
	       <li>本周 <div></div><br><span><?php echo $CurWeekQty; ?></span><span><?php echo "($CurWeekCount)"; ?></span></li>
	      <li><?php echo $nextWeek; ?>周+ <br><span><?php echo $NextWeekQty; ?></span><span><?php echo "($NextWeekCount)"; ?></span></li>
	 </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1690px;width:1080px;'>
<?php echo $ListSTR;
/*
$url='www.ymdoa.com:8818/iphoneAPI/managerApp/calendar/calendar_pushNotification.php'; 
$html = file_get_contents($url); 
include "d:/website/mc/iphoneAPI/subpush/dfp_nostandard_push.php";
*/
?>
</div>