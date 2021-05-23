<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";
      
      switch($Floor){
         case "3A":
         case "6":    $Floor=6;break;
         case "1A": 
         case "12":  $Floor=12;break;
         case "471": $Floor = 17;break;
         case "471C": $Floor = 14;break;
	     default:     $Floor=3;break;
      }

      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";	
     
 $m=0;		   
 //开单
$myResult=mysql_query("SELECT  S.Id,S.Mid,S.StuffId,S.StockId,(G.AddQty+G.FactualQty) AS cgQty, IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate) AS DeliveryDate,S.Qty,S.SendSign,
		     M.Date,M.CompanyId,P.Forshort,D.StuffCname,D.Picture,GM.PurchaseID,GM.Date AS cgDate,D.TypeId,
		     YEARWEEK(IF(G.StockId>0,G.DeliveryDate,CG.DeliveryDate),1)  AS Weeks    
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId=S.StockId 
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=C.mStockId
			WHERE  S.Estate=1  AND M.Floor='$Floor'   AND S.SendSign IN (0,1) ORDER BY M.Date,M.Id",$link_id);		
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
			     
			     $shDate=$myRow["Date"];
			     $DateChars="单";
			     $DateStr=GetDateTimeOutString($shDate,'');
			     $shHours=(strtotime($today)-strtotime($shDate))/3600;
			     $shColors=$shHours>6?"red_color":"";
			     $Mid=$myRow["Mid"];
			     //送货信息
			     $shSign=0;
			     $shInfoResult=mysql_query("SELECT S.Name  FROM $DataPublic.come_data S WHERE  S.Mid='$Mid'",$link_id);
			     if($shInfoRow = mysql_fetch_array($shInfoResult)){
			           if (strstr($shInfoRow["Name"],"快递")) $shSign=1;
			     }
			      
			       //配件属性
			      include "stuff_property.php";
			      
			     $Qty=number_format($Qty);
			     $cgQty=number_format($cgQty);
			     $Forshort="<span class='blue_color'>$Forshort</span>";
				 $ListSTR.="<table id='ListTable$m' name='ListTable[]'>
					<tr>
					    <td rowspan='2' width='120' class='$WeekClass $WeekColor'>$WeekSTR</td>
					    <td colspan='4' width='900' class='title'>$StuffId-$StuffCname</td>
					    <td  width='60'><img src='image/vehicle_$shSign.png' width='50'/> </td>
				   </tr>
				   <tr>
					    <td width='240'>$Forshort</td>
					    <td width='220' class='qty'><img src='image/order.png'/>$cgQty</td>
					    <td width='220' class='qty'><img src='image/register.png'/>$Qty</td>
					    <td width='280' class='time $shColors' colspan='2'>$DateStr<div>$DateChars</div></td>
				   </tr>";
				  // $ListSTR.="<tr><td colspan='4' style='height:40px;line-height:40px;'>&nbsp;</td></tr>";
				  //$ListSTR.="</table>";
				 

				//同一张单相同配件的备品 
				 $Mid=$myRow["Mid"];
				 $bpRemark="";
				$bpResult=mysql_query("SELECT S.Qty,S.StockId,S.SendSign  FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate=1 AND S.SendSign=2",$link_id);
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

				 
				if ($bpRemark!="" || $Remark!=""){
				        $ListSTR.="<tr><td  class='remark_icon'><img src='image/remark.png'/></td>
								          <td colspan='5' class='remark'>$bpRemark$Remark</td>
									      </tr>";
				}
				else{
					    $ListSTR.="<tr><td colspan='6' style='height:40px;line-height:40px;'>&nbsp;</td></tr>";
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

//今日到达数量
$shResult= mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty  FROM $DataIn.gys_shdate H 
				    LEFT JOIN $DataIn.gys_shsheet S  ON S.Id=H.Sid 
				    LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
				    WHERE   M.Floor='$Floor' AND  DATE_FORMAT(H.shDate,'%Y-%m-%d')='$curDate' ",$link_id));
$TodayQty=number_format($shResult["Qty"]);		    
				    

$WeekName=substr($curWeek, 4,2);

 $nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
  $nextWeek=$dateResult["NextWeek"];
  $nextWeek=substr($nextWeek, 4,2);
  
 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
 

?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
   <div id='weekdiv' class='float_left'><?php echo $WeekName; ?></div>
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