<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";

      //$Floor=$Floor==""?6:$Floor;//送货楼层
      $Floor=6;
      $LineNo=$LineNo==""?"D":$LineNo;
      $Line=$Line==""?"1":$Line;
      
      $LineResult=mysql_fetch_array(mysql_query("SELECT C.Id  FROM  $DataIn.qc_scline C  WHERE  C.LineNo='$LineNo'  AND C.Floor='$Floor' LIMIT 1",$link_id));
      $LineId=$LineResult["Id"]==""?1:$LineResult["Id"];
      
      $stuffidResult=mysql_fetch_array(mysql_query("SELECT StuffId  FROM  $DataIn.qc_currentcheck   WHERE  Id='$Line' LIMIT 1",$link_id));
      $checkStuffId=$stuffidResult["StuffId"]==""?0:$stuffidResult["StuffId"];
      
      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";	

     $m=0;
    
    $TotalCount=0;
    $WaitQty=0;//未品检总数
     
	$myResult=mysql_query("SELECT A.StuffId,SUM(A.cgQty) AS cgQty,SUM(A.Qty) AS Qty,A.Forshort,A.StuffCname,A.Picture,SUM(A.djQty) AS djQty,Max(A.QcDate) AS QcDate,A.shDate,K.datetime 
	FROM (
	SELECT  S.StuffId,(G.AddQty+G.FactualQty) AS cgQty,S.Qty, 
			     P.Forshort,D.StuffCname,D.Picture,IFNULL(SUM(C.Qty),0) AS djQty,Max(IFNULL(C.Date,'0000-00-00')) AS QcDate,T.shDate  
				FROM $DataIn.gys_shsheet S 
				LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
				LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
				LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id
				LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
				WHERE  S.Estate=2 AND S.SendSign IN(0,1)  AND M.Floor='$Floor' GROUP BY S.Id 
	)A 	
	LEFT JOIN (SELECT StuffId,MAX(datetime) AS datetime FROM $DataIn.qc_currentcheck GROUP BY StuffId) K ON K.StuffId=A.StuffId 	
	GROUP BY A.StuffId	ORDER BY FIELD(A.StuffId,$checkStuffId) DESC,datetime DESC,QcDate DESC,shDate",$link_id); 
  while($myRow = mysql_fetch_array($myResult)) { 
     $Qty=$myRow["Qty"];
     $DjQty=$myRow["djQty"];
     
     $TotalCount++;
     $WaitQty+= $Qty-$DjQty;
     if ($m<10){
		           
		          $StuffId=$myRow["StuffId"];
		          $StuffCname=$myRow["StuffCname"];
				  $Forshort=$myRow["Forshort"];
				  $cgQty=$myRow["cgQty"];
			     
			      $shDate=$myRow["shDate"];
			      $DateChars="到";
			      $DateStr=GetDateTimeOutString($shDate,$today);
			      $DateStr=str_replace("前", "", $DateStr);
			     
			     $shColors="";
			     if ($DjQty==0){
				     $shHours=(strtotime($today)-strtotime($shDate))/3600;
				     $shColors=$shHours>6?"red_color":"";
			     }
			     	     
			     //配件属性
			      include "stuff_property.php";
			     
			     $LineStr="";  $tableClass="";
			     $checkLineResult=mysql_query("SELECT Id,StuffId  FROM  $DataIn.qc_currentcheck",$link_id);
			     while($lineRow = mysql_fetch_array($checkLineResult)){
			        $className=$StuffId==$lineRow["StuffId"]?"green_bgcolor":"gray_bgcolor";
			        $Line_Id=$lineRow["Id"];
				    $LineStr.="<div class='$className'>$Line_Id</div>"; 
				    
				    if ($StuffId==$lineRow["StuffId"] && $Line_Id==$Line){
				       $tableClass=" tb_bgcolor2";
				    }
			     }
    
			     $Qty=number_format($Qty);
			     $cgQty=number_format($cgQty);
			     $DjQty=$DjQty==0?" ":number_format($DjQty); 
			     $Forshort="<span class='blue_color'>$Forshort</span>";
				 $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass'>
					<tr>
					   <td width='10'>&nbsp;</td>
					    <td colspan='3' width='840' class='title' style='word-wrap:break-word;'>$Forshort-$StuffCname</td>
					    <td width='230' class='line2'>$LineStr</td>
				   </tr>
				   <tr>
				        <td width='10'>&nbsp;</td>
				        <td width='280' class='qty' ><img src='image/order.png'/><span $cg_bgColor>$cgQty</span></td>
					    <td width='280' class='qty'><img src='image/register.png'/><span $LastBgColor>$Qty</span></td>
					    <td width='280' class='qty blue_color'><img src='image/djQtyIcon.png'/>$DjQty</td>
					    <td class='time $shColors'>$DateStr<div>$DateChars</div></td>
				   </tr>";
				   
             //备注 
             $Remark="";
			//同一张单相同配件的备品 
			 $Mid=$myRow["Mid"];
			 $bpRemark="";
			$bpResult=mysql_query("SELECT S.Qty,S.StockId,S.SendSign  FROM $DataIn.gys_shsheet S WHERE  S.StuffId='$StuffId' AND S.Estate=2  AND S.SendSign=2",$link_id);
			 if($bpRow = mysql_fetch_array($bpResult)) {
			      $bpQty=number_format($bpRow["Qty"]);
			        $sameResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums FROM $DataIn.gys_shsheet S WHERE S.StuffId='$StuffId' AND S.Estate>0  AND S.SendSign=0 ",$link_id));
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
 
$WaitQty=number_format($WaitQty);

 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
 
 //今日品检数量
 $QtyResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty   FROM $DataIn.qc_cjtj S  
			WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND S.LineId='$LineId'  
",$link_id));
$TodayQty=$QtyResult["Qty"]==""?0:$QtyResult["Qty"];


if ($Page!=2 || $TotalCount>0){//第二个页面为空时不显示
  //品检总人数
     $GroupId=601;
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
             <li class='text_left'><?php echo $WaitQty; ?></li>
             <li style='width:24px;'><div></div></li>
	         <li class='text_right'><span class='margin_right_15'><?php echo $TodayQty; ?></span></li>
   </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1690px;width:1080px;'>
<?php echo $ListSTR;?>
</div>
<?php } ?>