<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";

      switch($Floor){
         case "3A":
         case "6":    $Floor=6; $LineId="4";break;
	     default:     $Floor=3; $LineId="1,2,3";break;
      }

      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";	

 $m=0;
 	   
 //待入库
$mySql = "SELECT  S.Id,S.Mid,S.StuffId,S.StockId,G.POrderId,(G.AddQty+G.FactualQty) AS cgQty,
             IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS DeliveryDate,S.Qty AS shQty,S.SendSign,
             M.CompanyId,P.Forshort,D.StuffCname,D.Picture,YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1) AS Weeks,B.Date  
            FROM $DataIn.qc_mission H 
            LEFT JOIN $DataIn.qc_cjtj CJ ON CJ.Sid = H.Sid
            LEFT JOIN $DataIn.gys_shsheet S ON H.Sid=S.Id
            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
            LEFT JOIN $DataIn.qc_badrecord B ON B.shMid=S.Mid AND B.StockId=S.StockId AND B.StuffId=S.StuffId  
            LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=C.mStockId
            WHERE  H.rkSign=1 AND S.Estate=0 AND S.SendSign IN (0,1) AND M.Floor='$Floor' 
            GROUP BY S.Id  
            ORDER BY Date";
$myResult=mysql_query($mySql,$link_id);	

//按周以PI交期分组读取未出订单
$TotalOverQty=0; $OverCount=0;//逾期
$TodayRkQty=0;  $TodayRkCount=0;//本周
while($myRow = mysql_fetch_array($myResult)) {
         $Id=$myRow["Id"];
	     $Weeks=$myRow["Weeks"];
	    
	     $SendSign=$myRow["SendSign"];
	     
	     $checkResult=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS Qty   FROM  $DataIn.qc_cjtj  C WHERE  C.Sid='$Id'",$link_id));	
	     $Qty=$checkResult["Qty"];
	     if ($Qty<=0) continue;
	      
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
				  $shQty=$myRow["shQty"];
			     
			     $Date=$myRow["Date"];
			     $DateChars="检";
			     if ($Date!=""){
					     $DateStr=GetDateTimeOutString($Date,'');
					     $checkHours=(strtotime($today)-strtotime($Date))/60;
					     $checkHours=$checkHours>30?"red_color":"";
			     }
			     else{
				       $DateStr="";$shColors="";
			     }
			     
			      //配件属性
			      include "stuff_property.php";
			      
			     $Qty=number_format($Qty);
			     $cgQty=number_format($cgQty);
			     $shQty=number_format($shQty);
			     $Forshort="<span class='blue_color'>$Forshort</span>";
				 $ListSTR.="<table id='ListTable$m' name='ListTable[]'>
					<tr>
					    <td rowspan='2' width='120' class='$WeekClass $WeekColor'>$WeekSTR</td>
					    <td colspan='4' width='960' class='title'>$Forshort-$StuffCname</td>
				   </tr>
				   <tr>
					    <td width='230' class='qty'><img src='image/order.png'/>$cgQty</td>
					    <td width='230' class='qty'><img src='image/register.png'/>$shQty</td>
					    <td width='220' class='qty'><img src='image/djQtyIcon.png'/>$Qty</td>
					    <td width='280' class='time $checkHours'>$DateStr<div>$DateChars</div></td>
				   </tr>";
				//$ListSTR.="<tr><td colspan='5' style='height:40px;line-height:40px;'>&nbsp;</td></tr>";			      
		        //$ListSTR.="</table>";
		        
		         //备注 
             $Remark="";
             $RemarkResult=mysql_query("SELECT Remark  FROM $DataIn.qc_remark WHERE  Sid='$Id' ORDER BY Date DESC LIMIT 1",$link_id);
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
							          <td colspan='4' class='remark'>$bpRemark $Remark</td>
								      </tr>";
			}
			else{
				    $ListSTR.="<tr><td colspan='5' style='height:40px;line-height:40px;'>&nbsp;</td></tr>";
			}					     
								      
			 $ListSTR.="</table>";
			 
				 $m++;
			 }
}
				   
$TotalQty=$TotalOverQty+$CurWeekQty+$NextWeekQty;
$TotalCount=$OverCount+$CurWeekCount+$NextWeekCount;

//今日品检数量
 $QtyResult =mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS Qty 
           FROM $DataIn.qc_cjtj C   
           LEFT JOIN $DataIn.gys_shsheet S ON C.Sid=S.Id
			WHERE  DATE_FORMAT(C.Date,'%Y-%m-%d')='$curDate' AND C.LineId IN ($LineId) AND S.Estate=0 
",$link_id));
$TodayQty=$QtyResult["Qty"]==""?0:number_format($QtyResult["Qty"]);

$TotalQty=number_format($TotalQty);
$TotalOverQty=number_format($TotalOverQty);
$CurWeekQty=number_format($CurWeekQty);
$NextWeekQty=number_format($NextWeekQty);

$WeekName=substr($curWeek, 4,2);

 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
 
 $nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
  $nextWeek=$dateResult["NextWeek"];
  $nextWeek=substr($nextWeek, 4,2);
  
  $WeekName="<img src='image/storage.png' style='width:160px;height:160px;margin-bottom:15px;'>";
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
   <!--<div id='weekdiv' class='float_left'><?php echo $WeekName; ?></div>-->
   <div id='linediv' class='float_left'><?php echo $WeekName; ?></div>
   <ul id='quantity3' class='float_right' style='padding-top:50px;'>
             <li class='text_left'><?php echo $TotalQty; ?></li>
             <li style='width:24px;'><div></div></li>
	         <li class='text_right'><span><?php echo $TodayQty; ?></span></li>
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