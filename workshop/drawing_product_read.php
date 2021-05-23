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
$myResult=mysql_query("SELECT M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.sgRemark,C.Forshort,P.cName,P.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks,A.llEstate,A.BlDate,SC.DateTime,G.GroupName,IF(SC.Id>0,1,0) AS LineSign,IF(P.TestStandard=1,A.Type,0) AS EditType     
				 FROM ( 
				     SELECT S1.* FROM (
				          SELECT S0.POrderId,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty,SUM(S0.llEstate) AS llEstate,Max(S0.BlDate) AS BlDate,S0.Type  FROM (      
				             SELECT 
										S.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(SUM(L.Estate),0) AS llEstate,Max(BM.Date) AS BlDate,LD.Type  
				                        FROM $DataIn.yw1_ordermain M
										LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
										LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId 
										LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				                        LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
				                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
                                        LEFT JOIN $DataIn.ck5_llmain LM ON L.Mid=LM.Id 
                                        LEFT JOIN $DataIn.yw9_blmain BM ON L.Pid=BM.Id
                                        LEFT JOIN $DataIn.yw2_orderteststandard LD ON LD.POrderId=S.POrderId AND  LD.Type=9
				                        WHERE  S.scFrom>0 AND S.Estate=1  AND (P.TestStandard<>1 OR LD.Type=9)  AND ST.mainType<2
				                        AND NOT EXISTS(SELECT T.StuffId FROM  $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')  
				                        GROUP BY G.StockId 
				               )S0 GROUP BY S0.POrderId 
				     )S1 WHERE S1.blQty=S1.llQty 
				)A  
				LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId  
				LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
				LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
				LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
				LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId
				LEFT JOIN $DataIn.sc1_mission SC ON SC.POrderId=S.POrderId
				LEFT JOIN $DataIn.staffgroup G ON G.Id=SC.Operator 
				WHERE 1  ORDER BY EditType DESC,TestStandard,Weeks,llEstate,BlDate ",$link_id);		

//按周以PI交期分组读取未出订单
$OverCount=0;//逾期
$CurWeekCount=0;//本周
$NextWeekCount=0;//下周+
$DrawCount=0;
$m=0;
while($myRow = mysql_fetch_array($myResult)) {
	     $Weeks=$myRow["Weeks"];
	     $Qty=$myRow["Qty"];
	     
	      if ($Weeks<$curWeek){
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
	     
	     $TestStandard=$myRow["TestStandard"];
         $DrawCount+=$TestStandard==2?1:0;
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
	     $ScLine=$ScLine==""?"":"<div>$ScLine</div>";
	     
	     $POrderId=$myRow["POrderId"];
         
         
         $drawChars="";$drawDateSTR="";$auditChars="";$auditDateSTR="";
          
	     $BlDate=$myRow["BlDate"];
       
        $ProductId=$myRow["ProductId"];
		
		if ($TestStandard==2){
			 $ImagePath="../download/teststandard/T".$ProductId.".jpg";
             $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
             
             $drawDate=GetDateTimeOutString($BlDate,$img_mtime);
             $drawDateSTR=str_replace("前", "", $drawDate);
             $drawChars="<div>图</div>";
             
              $Minutes=(strtotime($today)-strtotime($img_mtime))/60;
             $auditDateSTR=GetDateTimeOutString($img_mtime,$today);
             $auditChars=$Minutes>30?"<div style='background-color: #FF0000;'>审</div>":"<div style='background-color: #888888;'>审</div>";
             
		}
		else{
		     $EditType=$myRow["EditType"];
		     $drawDateSTR=GetDateTimeOutString($BlDate,$today);
		     $drawChars=$EditType==9?"<div style='background-color: #FF00FF;'>图</div>":"<div style='background-color: #888888;'>图</div>";
		}
	      $Qty=number_format($Qty);
	      
	      $ListSTR.="<table id='ListTable$m' name='ListTable[]'  border='0' cellpadding='0' cellspacing='0'>
			<tr>
			    <td rowspan='2' width='120' class='week $WeekColor'><div>$Week1</div><div>$Week2</div></td>
			    <td colspan='3' width='912' class='title'><span>$Forshort-</span>$cName</td>
			    <td width='48' class='time'>&nbsp;</td>
		   </tr>
		   <tr>
			    <td width='360'>$OrderPO</td>
			    <td width='320' class='qty'><img src='image/order.png'/>$Qty</td>
			    <td width='232' class='time' style='padding-right:0;'>$drawDateSTR</td>
			    <td width='48' class='time'>$drawChars</td>
		   </tr>";
		   $ListSTR.="<tr>
						         <td colspan='3' style='height:40px;line-height:40px;'>&nbsp;</td>
						         <td width='232' class='time' style='padding-right: 0;'>$auditDateSTR</td>
			                     <td width='48' class='time'>$auditChars</td>
						   </tr>";
						  
		  $ListSTR.="</table>";
		  
		 
		 $m++;
	 }
	     
}
				   

$TotalQty=$TotalOverQty+$CurWeekQty+$NextWeekQty;
$TotalCount=$OverCount+$CurWeekCount+$NextWeekCount;

$WeekName="<img src='image/product.png' style='width:160px;height:160px;margin-bottom:15px;'>";

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
             <li style='width:24px;'><div></div></li>
             <li class='text_right'><span style=' font-size: 58pt;'><?php echo $DrawCount; ?></span>&nbsp;</li>
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