<?php
     //$Line=$Line==""?"C":$Line;
      include_once "tasks_function.php";
      include "../basic/parameter.inc";
      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
   
     $SC_TYPE='7100';  
     $Valuation=20;
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
 
   include "../iphoneAPI/subprogram/worktime_read.php";
   $Hours=substr($workTimes,0,2);
   $Minute=substr($workTimes,3,2);
   $wHours=$Hours+round($Minute/60,1);
   
   $TotalScQty=0;$TotalRG=0;$TotalNums=0;
   $ListSTR="";	$m=0; $TotalCount=0;
  $myResult = mysql_query(" SELECT S.GroupId,MAX(S.boxId) AS boxId,SUM(S.Qty) AS Qty,SUM(S.Qty*G.Price) AS RGAmount  
       	FROM $DataIn.sc1_cjtj S 
       	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
       	LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
		WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND S.TypeId='$SC_TYPE' AND D.TypeId='$SC_TYPE' 
		GROUP BY S.GroupId ORDER BY Qty" ,$link_id); 
while($myRow = mysql_fetch_assoc($myResult)){
      $GroupId=$myRow["GroupId"];
	  $Qty=$myRow["Qty"];
	  $RGAmount=round($myRow["RGAmount"]);
	  $ScLine=substr($myRow["boxId"], 0,1);
	  
	  $TotalCount++;
	  //当前人数
	  $GroupNums=0;
	 $GroupResult =mysql_query("SELECT G.GroupName,M.Number,M.Name,COUNT(*) AS Counts   
			FROM $DataIn.sc1_memberset  S
			LEFT JOIN $DataIn.staffgroup G  ON S.GroupId=G.GroupId
			LEFT JOIN $DataIn.checkinout C ON C.Number=S.Number AND  DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$curDate' AND C.CheckType='I'  
			LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader
			WHERE  S.Date='$curDate'    AND G.TypeId='$SC_TYPE'  AND S.GroupId='$GroupId' AND M.Estate=1 AND M.cSign=7 ",$link_id);
	 if ($GroupRow = mysql_fetch_array($GroupResult)) {
	     $LeaderNumber=$GroupRow["Number"];
		 $LeaderName=$GroupRow["Name"];
		 $GroupNums=$GroupRow["Counts"];
		 $ScLine=$GroupRow["GroupName"]==""?$ScLine:substr($GroupRow["GroupName"],-1,1);
		 //$ScLine=$ScLine==""?substr($GroupRow["GroupName"],-1,1):$ScLine;
		 //$GroupNums=$GroupNums . "/" . $GroupId;
	 }
	 
	 if ($LeaderNumber=="" && $GroupNums==0){
		  $GroupResult =mysql_query("SELECT G.GroupName,M.Number,M.Name 
		  FROM $DataIn.staffgroup G 
		  LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
		  WHERE  G.GroupId='$GroupId' AND M.Estate=1 AND M.cSign=7",$link_id);
		  if ($GroupRow = mysql_fetch_array($GroupResult)) {
			     $LeaderNumber=$GroupRow["Number"];
				 $LeaderName=$GroupRow["Name"];
	     }
	 }
	 
	 $TotalScQty+=$Qty;
	 $TotalRG+=$RGAmount;
	 $TotalNums+=$GroupNums;
	 
     $AverageRG=$GroupNums>0? round($RGAmount/$GroupNums):"0";
     $HourAverage=number_format($AverageRG/$wHours,1);
     $averageColor=$HourAverage<$Valuation?"red_color":"black_color";
 	$Qty=number_format($Qty);
 	$Qty.=strlen($Qty)<4?"&nbsp;":"";
 	
	$GroupNums=$GroupNums . "人";
	
	$Remark="";
 	$RemarkResult =mysql_query("SELECT Remark  FROM $DataIn.sc1_cjtj_log 
		  WHERE  GroupId='$GroupId' AND  Date='$curDate'",$link_id);
    if ($RemarkRow = mysql_fetch_array($RemarkResult)) {
            $Remark=$RemarkRow["Remark"];
    }
    
	$Rows=$Remark==""?1:2;
	$RowHeight=$Rows==2?" height='130px' ":"";
	
	$ListSTR.="
	  <table id='ListTable$m' name='ListTable[]' border='0' cellpadding='0' cellspacing='0' height='185px'>
		<tr $RowHeight>
		     <td   width='240' style='padding-left:25px;' class='border2' rowspan='$Rows'>
		              <div id='sclinebg'></div><div id='scline'>$ScLine</div>
		              <img id='leader2' src='photo/$LeaderNumber.png'/> 
				      <div  id='leader_name2' style='margin:-50px 0px 0px 0px;'><span>$LeaderName</span></div>
		     </td>
		     <td  width='200' class='sccount border2 text_right'>$GroupNums</td>
		     <td  width='280' class='sccount border2 text_right' ><span class='$averageColor'>¥$HourAverage</span><span id='valuation_icon'>时</span></td>
		     <td  width='360' class='scqty  border2'>$Qty</td>
	   </tr>";
	   
	  if ($Rows==2){
		  $ListSTR.="<tr><td  colspan='3' class='remark' style='vertical-align:top;height:45px;'><img src='image/remark.png' />$Remark</td></tr>";
	  }
	   
	   $ListSTR.="</table>";
	  // <td  width='200' class='sccount border2 text_right'>¥$AverageRG</td>
	   $m++;
}

//上班人员统计
include "staff_counts.php";

//今日生产数量
$TotalScQty=number_format($TotalScQty);

//本月生产数量
$curMonth=date("Y-m");
$ScedResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS ScQty    
		FROM $DataIn.sc1_cjtj S 
		WHERE  DATE_FORMAT(S.Date,'%Y-%m')='$curMonth' AND S.TypeId='$SC_TYPE' ",$link_id));
$MonthScQty=$ScedResult["ScQty"]==""?0:number_format($ScedResult["ScQty"]);

//人均产值
$AverageRG=$TotalNums>0? round(($TotalRG/$workNums)/$wHours,1):"0";
$AverageRG_Color=$AverageRG<$Valuation?"red_color":"black_color";
//日人均产值

$AverageRG1=round(($TotalRG/$workNums),1);
$Valuation1=$Valuation*$wHours;
$Valuation2=$Valuation1*$workNums;
$Valuation2=number_format($Valuation2);


$TotalRG=number_format($TotalRG);

 $WeekName=substr($curWeek, 4,2);
 $upTime=date("H:i:s");
?>
<input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
  <input type='hidden' id='FinishCount' name='FinishCount' value='0'>
  
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
   <ul id='count'>
	      <li style='width:280px;'><div></div>时人均 <br><span class='<?php echo $AverageRG_Color?>'><?php echo $AverageRG; ?></span><span class='valuation'>/<?php echo "¥" .$Valuation; ?></span><span id='valuation_icon'>估</span>
	      </li>
	      <li style='width:370px;'><div></div>日人均<br><?php echo $AverageRG1; ?><span class='valuation'>/<?php echo "¥" .$Valuation1; ?></span><span id='valuation_icon'>估</span>
          </li>
	      <li style='width:430px;'>日产值<br><?php echo  $TotalRG; ?><span class='valuation'>/<?php echo "¥" .$Valuation2; ?></span><span id='valuation_icon'>估</span>
	      </li>
	 </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1560px;width:1080px;'>
<?php echo $ListSTR;?>
</div>