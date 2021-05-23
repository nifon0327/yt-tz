<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";

     switch($Floor){
         case "3A":
         case "6":    $Floor=6;$CheckSign=0;break;//只有抽检
         case "1A":  
         case "12": $Floor=12;$CheckSign=0;break;//只有抽检
	     default:     $Floor=3;$CheckSign=strlen($CheckSign)==0?1:$CheckSign;break;
      }

      //$LineResult=mysql_fetch_array(mysql_query("SELECT C.Id  FROM  $DataIn.qc_scline C  WHERE  C.LineNo='$Line'  AND C.Floor='$Floor' LIMIT 1",$link_id));
      //$LineId=$LineResult["Id"]==""?1:$LineResult["Id"];
      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";	
     
	 $SearchRows="AND D.CheckSign='$CheckSign' ";

 $m=0;
 	   
 //品检任务
$myResult=mysql_query("SELECT  S.Id,S.Mid,S.StuffId,S.StockId,G.POrderId,(G.AddQty+G.FactualQty) AS cgQty,S.Qty,S.SendSign,Max(S.shDate) AS shDate,
		     M.CompanyId,P.Forshort,D.StuffCname,D.Picture,GM.PurchaseID,GM.Date AS cgDate,D.TypeId,
		     IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS  DeliveryDate,
		     YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1)  AS Weeks, IFNULL(W.ReduceWeeks,1) AS ReduceWeeks 
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0 
			LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=C.mStockId
			WHERE  S.Estate=2  AND S.SendSign IN (0,1) AND M.Floor='$Floor' $SearchRows  AND NOT EXISTS(SELECT H.Sid FROM $DataIn.qc_mission H WHERE H.Sid=S.Id) 
		   GROUP BY S.Id ORDER BY shDate,SendSign DESC,Weeks,ReduceWeeks",$link_id);	

//按周以PI交期分组读取未出订单
$TotalOverQty=0; $OverCount=0;//逾期
$CurWeekQty=0;  $CurWeekCount=0;//本周
$NextWeekQty=0;  $NextWeekCount=0;//下周+
while($myRow = mysql_fetch_array($myResult)) {
	     $Weeks=$myRow["Weeks"];
	     $Qty=$myRow["Qty"];
	     $SendSign=$myRow["SendSign"];
	     
	      if ($Weeks<$curWeek || $SendSign==1){
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
                
                 if ($SendSign==0){
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
		           
		          $StuffId=$myRow["StuffId"];
		          $StuffCname=$myRow["StuffCname"];
				  $Forshort=$myRow["Forshort"];
				  $cgQty=$myRow["cgQty"];
			     
			     $shDate=$myRow["shDate"];
			     $DateChars="到";
			     if ($shDate!=""){
					     $DateStr=GetDateTimeOutString($shDate,'',1);
					     $shHours=(strtotime($today)-strtotime($shDate))/3600;
					     $shColors=$shHours>6?"red_color":"";
			     }
			     else{
				       $DateStr="";$shColors="";
			     }
			  
			      //订单交期与采购交期同周
			     $cg_bgColor=$myRow["ReduceWeeks"]==1?"":" style='background-color: #CCE3F0' ";
			     
			     //最后一个配件
			     $POrderId=$myRow["POrderId"];
			     include "stuff_blcheck.php";
			     
			      //配件属性
			      include "stuff_property.php";
			      
			     $Qty=number_format($Qty);
			     $cgQty=number_format($cgQty);
			     $PicChars="&nbsp;";
			     switch($myRow["Picture"]){
				     case 0: $PicChars="<div style='background-color: #888888;'>图</div>";break;
				     case 2: $PicChars="<div style='background-color: #FF00FF;'>图</div>";break;
				     case 4: $PicChars="<div style='background-color: #FFD800;'>图</div>";break;
			     }
			     
			     $Forshort="<span class='blue_color'>$Forshort</span>";
				 $ListSTR.="<table id='ListTable$m' name='ListTable[]'>
					<tr>
					    <td rowspan='2' width='120' class='$WeekClass $WeekColor'>$WeekSTR</td>
					    <td colspan='4' width='912' class='title'>$StuffId-$StuffCname</td>
					     <td width='48' class='time'>$PicChars</td>
				   </tr>
				   <tr>
				        <td width='240'>$Forshort</td>
					    <td width='220' class='qty'><img src='image/order.png'/><span $cg_bgColor>$cgQty</span></td>
					    <td width='220' class='qty'><img src='image/register.png'/><span $LastBgColor>$Qty</span></td>
					    <td width='232' class='time $shColors' style='padding-right:0;'>$DateStr</td>
					    <td width='48' class='time'><div>$DateChars</div></td>
				   </tr>";
				 
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
				 
				 	  //送货备注
			$Id=$myRow["Id"];
            $Remark="";$RemarkDate="";
            $CheckRemark=mysql_query("SELECT R.Remark FROM $DataIn.gys_shremark  R 
				            WHERE R.Sid='$Id' ORDER BY R.Id DESC LIMIT 1",$link_id);       
        	if($RemarkRow = mysql_fetch_array($CheckRemark)){
			         $Remark=$RemarkRow["Remark"];
				    // $RemarkDate=$RemarkRow["Date"];
					// $RemarkDate=GetDateTimeOutString($RemarkDate,'');
			}
			
			//$NoPicSTR=$myRow["Picture"]==1?"":"<span class='red_color'>无配件图片;</span>";
			
				if ($bpRemark!="" || $Remark!=""){
				        $ListSTR.="<tr><td  class='remark_icon'><img src='image/remark.png'/></td>
								          <td colspan='3' class='remark'>$bpRemark $Remark</td>
								          <td  class='remark text_right'><span class='$QcDateColor'>$QcDateStr</span></td>
								          <td width='48' class='time'>&nbsp;</td>
									      </tr>";
				}
				else{
					    $ListSTR.="<tr><td colspan='4' style='height:40px;line-height:40px;'>&nbsp;</td>
										          <td  class='remark text_right'><span class='$QcDateColor'>$QcDateStr</span></td>
										          <td width='48' class='time'>&nbsp;</td>
										          </tr>";
				}					     
								      
		        $ListSTR.="</table>";
				 $m++;
			 }
}
				   
$TotalQty=$TotalOverQty+$CurWeekQty+$NextWeekQty;
$TotalCount=$OverCount+$CurWeekCount+$NextWeekCount;

$TotalQty=number_format($TotalQty);
$TotalOverQty=number_format($TotalOverQty);
$CurWeekQty=number_format($CurWeekQty);
$NextWeekQty=number_format($NextWeekQty);

$WeekName=substr($curWeek, 4,2);

 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
// echo $upTime;
 $nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
  $nextWeek=$dateResult["NextWeek"];
  $nextWeek=substr($nextWeek, 4,2);
  
 $ImageName=$CheckSign==1?"checksign_1.png":"checksign_0.png";
 $WeekName="<img src='image/$ImageName' style='width:160px;height:160px;margin-bottom:15px;'>";

if (!($Floor==3  && $TotalCount==0 && $CheckSign==0)) {

?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
   <div id='linediv' class='float_left'><?php echo $WeekName; ?></div>
   <ul id='quantity3' class='float_right' style='padding-top:50px;'>
             <li class='text_left'><?php echo $TotalQty; ?></li>
             <li style='width:24px;'><div></div></li>
	         <li class='text_right'><span style='color:#FF0000;'><?php echo $TotalOverQty; ?></span></li>
   </ul>
   <ul id='count' class='border3'>
           <li>补货/逾期 <div></div><br><span class='red_color'><?php echo $TotalOverQty; ?> </span><span><?php echo  "($OverCount)"; ?> </span></li>
	       <li>本周 <div></div><br><span><?php echo $CurWeekQty; ?></span><span><?php echo "($CurWeekCount)"; ?></span></li>
	      <li><?php echo $nextWeek; ?>周+ <br><span><?php echo $NextWeekQty; ?></span><span><?php echo "($NextWeekCount)"; ?></span></li>
	 </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1690px;width:1080px;'>
<?php echo $ListSTR;?>
</div>
<?php } ?>