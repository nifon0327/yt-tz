<?php
      include_once "tasks_function.php";
      include "../basic/downloadFileIP.php";
      include "../basic/parameter.inc";

      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";	
   
 //已到达配件
$myResult=mysql_query("SELECT  S.Id,S.Mid,S.StuffId,S.StockId,G.POrderId,(G.AddQty+G.FactualQty) AS cgQty,G.DeliveryDate,S.Qty,S.SendSign,Max(T.shDate) AS shDate,
		     M.CompanyId,P.Forshort,D.StuffCname,D.Picture,GM.PurchaseID,GM.Date AS cgDate,D.TypeId,YEARWEEK(G.DeliveryDate,1) AS Weeks,
		     IFNULL(W.ReduceWeeks,1) AS ReduceWeeks 
			FROM $DataIn.gys_shsheet S 
			LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id  
			LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0 
			WHERE  S.Estate=2  AND S.SendSign IN (0,1)  AND D.Picture<>1  GROUP BY S.Id ORDER BY shDate,SendSign DESC,Weeks,ReduceWeeks",$link_id);	

$OverCount=0;//逾期
$CurWeekCount=0;//本周
$NextWeekCount=0;//下周+
$DrawCount=0;
$m=0;
while($myRow = mysql_fetch_array($myResult)) {
	     $Weeks=$myRow["Weeks"];
	     $Qty=$myRow["Qty"];
	     $SendSign=$myRow["SendSign"];
	     
	      if ($Weeks<$curWeek || $SendSign==1){
		        $OverCount++;
	      }
	     else{
		        if ($Weeks==$curWeek){
		              $CurWeekCount++;
		        }
		        else{
			         $NextWeekCount++;
		        }
	     }
         
         $Picture=$myRow["Picture"];
		 $DrawCount+=$Picture==2?1:0;
		
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
				  $cgQty=number_format($myRow["cgQty"]);
         
                
                
                $drawChars="";$drawDateSTR="";$auditChars="";$auditDateSTR="";
          
        $shDate=$myRow["shDate"];
		
		if ($Picture==2){ 
           $ImagePath="$donwloadFileIP/download/stufffile/".$StuffId. ".jpg";
          
           $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
             
             $drawDate=GetDateTimeOutString($shDate,$img_mtime);
             $drawDateSTR=str_replace("前", "", $drawDate);
             $drawChars="<div>图</div>";
             
             $auditDateSTR=GetDateTimeOutString($img_mtime,$today);
             $auditChars="<div style='background-color: #888888;'>审</div>";
		}
		else{
		     $drawDateSTR=GetDateTimeOutString($shDate,$today);
		     $drawChars="<div style='background-color: #888888;'>图</div>";
		}
	      $Qty=number_format($myRow["Qty"]);
	      
	      $ListSTR.="<table id='ListTable$m' name='ListTable[]'  border='0' cellpadding='0' cellspacing='0'>
			<tr>
			    <td rowspan='2' width='120' class='week $WeekColor'><div>$Week1</div><div>$Week2</div></td>
			    <td colspan='4' width='912' class='title'>$StuffId-$StuffCname</td>
			    <td width='48' class='time'>&nbsp;</td>
		   </tr>
		   <tr>
			    <td width='280'>$Forshort</td>
			    <td width='210' class='qty'><img src='image/order.png'/>$cgQty</td>
			    <td width='210' class='qty'><img src='image/register.png'/>$Qty</td>
			    <td width='212' class='time' style='padding-right:0;'>$drawDateSTR</td>
			    <td width='48' class='time'>$drawChars</td>
		   </tr>";
		   $ListSTR.="<tr>
						         <td colspan='4' style='height:40px;line-height:40px;'>&nbsp;</td>
						         <td width='232' class='time' style='padding-right: 0;'>$auditDateSTR</td>
			                     <td width='48' class='time'>$auditChars</td>
						   </tr>";
						  
		  $ListSTR.="</table>";
		  
		 
		 $m++;
	 }
	     
}
				   
$TotalCount=$OverCount+$CurWeekCount+$NextWeekCount;

$WeekName="<img src='image/stuff.png' style='width:160px;height:160px;margin-bottom:15px;'>";

 $upTime=date("H:i:s");
 
 
 $nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
  $nextWeek=$dateResult["NextWeek"];
  $nextWeek=substr($nextWeek, 4,2);
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
     <div id='linediv' class='float_left'><?php echo $WeekName; ?></div>
	 <ul id='quantity3' class='float_right' style='padding-top:50px;'>
             <li class='text_left' style=' font-size: 58pt;'><?php echo $TotalCount; ?></li>
             <li style='width:24px;'>&nbsp;</li>
             <li class='text_right'>&nbsp;</li>
   </ul>
   <ul id='count' class='border3'>
           <li>逾期 <div></div><br><span class='red_color'><?php echo $OverCount; ?> </span></li>
	       <li>本周 <div></div><br><span><?php echo $CurWeekCount; ?></span></li>
	      <li><?php echo $nextWeek; ?>周+ <br><span><?php echo $NextWeekCount; ?></span></li>
	 </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1690px;width:1080px;'>
<?php echo $ListSTR;?>
</div>