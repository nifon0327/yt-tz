<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";
      
      $SearchRows="";
      switch($Floor){
         case "3A":
         case "6":    $Floor=6; $Line="D"; break;
	     default:     $Floor=3;$Line=$Line==""?"A":$Line; break;
      }
      
      $LineResult=mysql_fetch_array(mysql_query("SELECT C.Id  FROM  $DataIn.qc_scline C  WHERE  C.LineNo='$Line'  AND C.Floor='$Floor' LIMIT 1",$link_id));
      $LineId=$LineResult["Id"]==""?1:$LineResult["Id"];
      $SearchRows=" AND  H.LineId='$LineId' ";
      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";	

 $m=0;

$TotalQty=0; $TotalCount=0;//品检总数
$CurQty=0;  $CurCount=0;//品检中
$QcedQty=0;  $QcedCount=0;//待处理

$WaitQty=0; $WaitCount=0;   
 //品检任务
$myResult=mysql_query("SELECT  S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty,S.Qty,S.SendSign,G.POrderId, 
             IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS  DeliveryDate,
		     M.CompanyId,P.Forshort,D.StuffCname,D.Picture,D.TypeId,YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1)  AS Weeks,H.DateTime,
		   Max(IFNULL(C.Date,Now())) AS QcDate,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks,H.Estate,Max(T.shDate) AS shDate 
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id 
			LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
			LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id   
			LEFT JOIN $DataIn.cg1_stuffcombox CS ON CS.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=CS.mStockId
			WHERE  S.Estate=2  AND M.Floor='$Floor'  AND S.SendSign IN(0,1)  $SearchRows GROUP BY S.Id ORDER BY Estate,QcDate,H.DateTime,Weeks,ReduceWeeks",$link_id);//shDate,
while($myRow = mysql_fetch_array($myResult)) {
         $Id=$myRow["Id"];
         $Mid=$myRow["Mid"];
	     $Weeks=$myRow["Weeks"];
	     $Qty=$myRow["Qty"];
	      
	     $TotalQty+=$Qty; $TotalCount++;
	     
	     //已登记数量
	     $StuffId=$myRow["StuffId"];
         $djResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty  FROM $DataIn.qc_cjtj WHERE Sid='$Id' AND StuffId='$StuffId' ",$link_id));
		  $DjQty=$djResult["Qty"];
	     
	     $QcDateColor="";$QcDateStr="";
	     $tableClass=" tb_bgcolor0";//默认
	     if ($DjQty>0){
		      $QcDate=$myRow["QcDate"];
		      $QcDateStr=GetDateTimeOutString($QcDate,'');
		      $QcMinutes=(strtotime($today)-strtotime($QcDate))/60;
		       if ($QcMinutes>30 || $DjQty==$Qty){
		             $QcedQty+=$DjQty;  $QcedCount++;
		             $tableClass=" tb_bgcolor1 ";  
		             $QcDateColor=$QcMinutes>30? " red_color ":"";
		      }
		      else{
			        $CurQty+=$Qty;  $CurCount++;
			        $tableClass=" tb_bgcolor2";
		      }
	     }
	     else{
		      $WaitQty+=$Qty;$WaitCount++;
	     }
         
         if ($m<10){
	              if ($Weeks>0){
			             $StockId=$myRow["StockId"];
					     $Week1=substr($Weeks, 4,1);
					     $Week2=substr($Weeks, 5,1);
					     $WeekColor=$curWeek>$Weeks?'bgcolor_red':'bgcolor_black';
					     $tdColor=$curWeek>$Weeks?'red_color':'black_color';
					     $WeekClass="week";
				        $WeekSTR="<div>$Week1</div><div>$Week2</div>";
		         }
		         else{
			            $WeekClass="week2";
				        $WeekSTR="<div>补</div>";
		         }
		         
		          $StuffCname=$myRow["StuffCname"];
				  $Forshort=$myRow["Forshort"];
				  $cgQty=$myRow["cgQty"];
			     
			     $shDate=$myRow["DateTime"];
			     $DateChars="配";
			     $DateStr=GetDateTimeOutString($myRow["QcDate"],$today,1);
			     //$DateStr=str_replace("前", "", $DateStr);
			     
			      $shColors="";
			     if ($DjQty==0){
				     $shHours=(strtotime($today)-strtotime($shDate))/3600;
				     $shColors=$shHours>6?"red_color":"";
			     }
			  
			     //订单交期与采购交期同周
			     $cg_bgColor=$myRow["ReduceWeeks"]==1?"":" style='background-color:#D9EAF4' ";
			     
			     //最后一个配件
			     $POrderId=$myRow["POrderId"];
			     include "stuff_blcheck.php";		
			     	     
			     //配件属性
			      include "stuff_property.php";
    
			     $Qty=number_format($Qty);
			     $cgQty=number_format($cgQty);
			     $DjQty=$DjQty==0?" ":number_format($DjQty); 
			     $Forshort="<span class='blue_color'>$Forshort</span>";
				 $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass'>
					<tr>
					    <td rowspan='2' width='120' class='$WeekClass  $WeekColor'>$WeekSTR</td>
					    <td colspan='4' width='960' class='title'>$Forshort-$StuffCname</td>
				   </tr>
				   <tr>
				        <td width='230' class='qty' ><img src='image/order.png'/><span $cg_bgColor>$cgQty</span></td>
					    <td width='230' class='qty'><img src='image/register.png'/><span $LastBgColor>$Qty</span></td>
					    <td width='220' class='qty blue_color'><img src='image/djQtyIcon.png'/>$DjQty</td>
					    <td width='280' class='time $shColors'>$DateStr<div>$DateChars</div></td>
				   </tr>";
				   
             //备注 
             $Remark="";
             $RemarkResult=mysql_query("SELECT Remark  FROM $DataIn.qc_remark WHERE  Sid='$Id' ORDER BY Date DESC, Id Desc LIMIT 1",$link_id);
			 if($RemarkRow = mysql_fetch_array($RemarkResult)) {
			      $Remark=$RemarkRow["Remark"];
			 }

			//同一张单相同配件的备品 
			 $Mid=$myRow["Mid"];
			 $bpRemark="";
			$bpResult=mysql_query("SELECT S.Qty,S.StockId,S.SendSign  FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate=2  AND S.SendSign=2",$link_id);
			 if($bpRow = mysql_fetch_array($bpResult)) {
			      $bpQty=number_format($bpRow["Qty"]);
			        $sameResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate>0  AND S.SendSign=0 ",$link_id));
				   $Nums=$sameResult["Nums"];
				   $bpRemark=$bpQty . "pcs备品($Nums);";
			 }
			if ($bpRemark!="" || $Remark!=""){
			        $ListSTR.="<tr><td  class='remark_icon'><img src='image/remark.png'/></td>
							          <td colspan='3' class='remark'>$bpRemark $Remark</td>
							          <td  class='remark text_right'><span class='$QcDateColor'>$QcDateStr</span> &nbsp;&nbsp;&nbsp;&nbsp;</td>
								      </tr>";
			}
			else{
				    $ListSTR.="<tr><td colspan='4' style='height:40px;line-height:40px;'>&nbsp;</td>
									          <td  class='remark text_right'><span class='$QcDateColor'>$QcDateStr</span> &nbsp;&nbsp;&nbsp;&nbsp;</td>
									  </tr>";
			}					     
								      
			 $ListSTR.="</table>";
			 $m++;
		}
}

if ($ListSTR==""){
	if ($bfListSTR[2]!=""){//补货待处理
			         $ListSTR.=$bfListSTR[2];$bfListSTR[2]="";
		      }
		             
	          if ($bfListSTR[1]!=""){//补货品检中
			         $ListSTR.=$bfListSTR[1];$bfListSTR[1]="";
		      }
		             
		      if ($bfListSTR[0]!=""){//补货待处理
			         $ListSTR.=$bfListSTR[0];$bfListSTR[0]="";
		      }

}
				   
//$WaitQty=$TotalQty-$CurQty-$QcedQty;
//$WaitCount=$TotalCount-$CurCount-$QcedCount;

$TotalQty=number_format($TotalQty);
$CurQty=number_format($CurQty);
$QcedQty=number_format($QcedQty);
$WaitQty=number_format($WaitQty);

 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
 
 //今日品检数量
 $QtyResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty   FROM $DataIn.qc_cjtj S  
			WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND S.LineId='$LineId'  
",$link_id));
$TodayQty=$QtyResult["Qty"]==""?0:number_format($QtyResult["Qty"]);

if ($Page!=2 || $TotalCount>0){//第二个页面为空时不显示
  //品检总人数
     $GroupId=604;
	 $BranchResult =mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts   
			FROM $DataPublic.staffmain M  
			WHERE  M.GroupId='$GroupId'  AND M.Estate=1 AND M.cSign=7 ",$link_id));
	$BranchNums=$BranchResult["Counts"];	
	
	//当前人数
	 $GroupResult =mysql_query("SELECT M.Number,M.Name,COUNT(*) AS Counts   
			FROM $DataPublic.staffmain M  
			LEFT JOIN $DataIn.checkinout C ON C.Number=M.Number AND  DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$curDate' AND C.CheckType='I'  
			WHERE  M.GroupId='$GroupId' AND M.Estate=1 AND M.cSign=7 AND C.Id>0",$link_id);
	 if ($GroupRow = mysql_fetch_array($GroupResult)) {
	    // $LeaderNumber=$GroupRow["Number"];
		// $LeaderName=$GroupRow["Name"];
		 $GroupNums=$GroupRow["Counts"];
	 }
	 
	 //请假人数
	 $OverTime=date("Y-m-d") . " 17:00:00";
	 $LeaveResult =mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts  FROM (SELECT K.Number   
			FROM $DataPublic.kqqjsheet K
			LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
			WHERE (K.EndDate>=NOW() OR K.EndDate>='$OverTime') AND M.GroupId='$GroupId' AND M.cSign=7 AND M.Estate=1  GROUP BY K.Number)A ",$link_id));
	$LeaveNums=$LeaveResult["Counts"];
	$GroupNums-=$LeaveNums;

/*
if ($Line=="D"){
	     $WeekDiv="weekdiv";
	     $WeekName=substr($curWeek, 4,2);
}
else{
	    $WeekDiv="linediv";
	    $WeekName=$Line;
}	
*/
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
   <div id='linediv' class='float_left'><?php echo $Line; ?></div>
   <ul id='group' class='float_right'>
	      <li><img src='image/group_staff.png'/><?php echo $BranchNums; ?>人</li>
	      <li><img src='image/working_staff.png'/><?php echo $GroupNums; ?>人</li>
	      <li><img src='image/leave_staff.png' style='margin-top:3px;'/><?php echo $LeaveNums; ?>人</li>
	 </ul>
   <ul id='quantity3' class='float_right'>
             <li class='text_left'><?php echo $TotalQty; ?></li>
             <li style='width:24px;'><div></div></li>
	         <li class='text_right'><span class='margin_right_15'><?php echo $TodayQty; ?></span></li>
   </ul>
   <ul id='count' class='border3'>
           <li>待处理 <div></div><br><span class='red_color'><?php echo $QcedQty; ?> </span><span><?php echo  "($QcedCount)"; ?> </span></li>
	       <li>品检中 <div></div><br><span><?php echo $CurQty; ?></span><span><?php echo "($CurCount)"; ?></span></li>
	       <li>待检<br><span><?php echo $WaitQty; ?></span><span><?php echo "($WaitCount)"; ?></span></li>
	 </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1690px;width:1080px;'>
<?php echo $ListSTR;?>
</div>
<?php } ?>