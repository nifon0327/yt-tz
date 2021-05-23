<?php
//待出按客户排序
      include_once "tasks_function.php";
      include "../basic/parameter.inc";

      $Floor=$Floor==""?3:$Floor;//送货楼层
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      
     $curDate=date("Y-m-d");
     $today=date("Y-m-d H:i:s");
     
     $ListSTR="";	
     $SearchRows="";

     $m=0;
     
$staffStyle=" style='font-size:25pt;color:#308CC0;position: absolute;margin:0px 0px 10px 12px;display:block;'";
$boxStyle="  style='font-size: 25pt;text-align: left;color:#BBBBBB;vertical-align:bottom;'";
$myResult=mysql_query("
    SELECT M.CompanyId,C.Forshort,IFNULL(IFNULL(SM.FinishTime,A.Date),D.Date) AS FinishTime,CM.chDate,COUNT(*) AS Counts,
    SUM(S.Qty-IFNULL(B.ShipedQty,0)) AS Qty,SUM((S.Qty-IFNULL(B.ShipedQty,0))*S.Price*U.Rate) AS Amount,
    SUM(IF(IFNULL(B.ShipedQty,0)=0,A.Boxs,CEIL((S.Qty-B.ShipedQty)*(A.Boxs/S.Qty)))) AS Boxs,
    SUM(IF(TIMESTAMPDIFF(day,IFNULL(IFNULL(SM.FinishTime,A.Date),D.Date),Now())>=5,S.Qty,0)) AS OverQty,
    SUM(IF(TIMESTAMPDIFF(day,IFNULL(IFNULL(SM.FinishTime,A.Date),D.Date),Now())>=5,IF(IFNULL(B.ShipedQty,0)=0,A.Boxs,CEIL((S.Qty-B.ShipedQty)*(A.Boxs/S.Qty))),0)) AS OverBoxs,
    SUM(IF(TIMESTAMPDIFF(day,IFNULL(IFNULL(SM.FinishTime,A.Date),D.Date),Now())>=5,1,0)) AS OverCount,F.Name AS StaffName    
			FROM $DataIn.yw1_ordersheet S 
			INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
            INNER JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN  $DataPublic.staffmain F ON F.Number=C.Staff_Number 
            LEFT JOIN  $DataPublic.currencydata U ON U.Id=C.Currency 
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
            LEFT JOIN $DataIn.sc1_mission SM ON SM.POrderId=S.POrderId 
            LEFT JOIN (
                          SELECT S.POrderId,SUM(P.Qty) AS ShipedQty FROM $DataIn.yw1_ordersheet S 
                           LEFT JOIN $DataIn.ch1_shipsheet P ON P.POrderId=S.POrderId  
                           WHERE S.Estate>=1  AND P.Qty>0 GROUP BY  S.POrderId) B ON B.POrderId=S.POrderId 
            LEFT JOIN (SELECT CompanyId,Max(OPdatetime) AS ChDate FROM  $DataIn.ch1_shipmain GROUP BY CompanyId)CM ON CM.CompanyId=M.CompanyId
            LEFT JOIN (
                     SELECT S.POrderId,Max(C.Date) AS Date,COUNT(*) AS Boxs FROM $DataIn.yw1_ordersheet S 
                     LEFT JOIN $DataIn.sc1_cjtj C ON C.POrderId=S.POrderId 
                     WHERE S.Estate>1 GROUP BY S.POrderId
             ) A ON A.POrderId=S.POrderId 
		    LEFT JOIN (
		             SELECT S.POrderId,IFNULL(Max(M.Date),Max(BM.Date)) AS Date FROM $DataIn.yw1_ordersheet S 
		             LEFT JOIN $DataIn.ck5_llsheet L ON L.POrderId=S.POrderId 
		             LEFT JOIN $DataIn.ck5_llmain M ON M.Id=L.Mid 
		             LEFT JOIN $DataIn.yw9_blmain BM ON BM.Id=L.Pid 
		             WHERE S.Estate>1 GROUP BY S.POrderId
		     ) D ON D.POrderId=S.POrderId 
		    WHERE S.Estate>1  GROUP BY M.CompanyId ORDER BY Amount DESC ",$link_id);
  $TotalOverQty=0; $TotalOverCount=0;//逾期
  $TotalShipQty=0;	$TotalShipCount=0;//待出货方式
  $TotalAmount=0;$ListArray=array();
  $TotalQty=0;$TotalCount=0;$m=0;
  while($myRow = mysql_fetch_array($myResult)) {
        $Forshort=$myRow["Forshort"];
        $Qty=$myRow["Qty"];
        $Amount=$myRow["Amount"];
        $TotalAmount+=$Amount;
        $Counts=$myRow["Counts"];
        $TotalQty+=$Qty;
        
        $OverQty=$myRow["OverQty"];
        $OverCount=$myRow["OverCount"];
        $TotalOverQty+=$OverQty;
        $TotalOverCount+=$OverCount;
        
        $ShipIMG=$ShipCount>0?"<img src='image/doubt.png'/>":"";
        $OverIMG=$OverCount>0?"<img src='image/5d.png'/>":"";
        
        $OverBoxs=$myRow["OverBoxs"];
        $Boxs=$myRow["Boxs"];
        $StaffName=$myRow["StaffName"];
        
                
        if ($m<10){
            $ListArray[]=array("Forshort"=>"$Forshort",
								        "staffStyle"=>"$staffStyle",
								        "StaffName"=>"$StaffName",
								        "Qty"=>"$Qty",
								        "Boxs"=>"$Boxs",
								        "Amount"=>"$Amount",
								        "OverIMG"=>"$OverIMG",
								        "OverQty"=>"$OverQty",
								        "OverBoxs"=>"$OverBoxs",
								        "OverCount"=>"$OverCount"
								        );
           /*
           $Qty=number_format($Qty);
           $OverQty=number_format($OverQty);
           $Amount=number_format($Amount);
           if ($OverCount>0){
	           $ListSTR.="<table id='ListTable$m' name='ListTable[]' border='0' cellpadding='0' cellspacing='0' height='185px' style='word-break: keep-all;
white-space:nowrap;'>
						   <tr height='75%'>
						        <td width='380'   class='c_title' rowspan='2'>&nbsp;$Forshort<div $staffStyle>$StaffName</div></td>
						        <td width='55'     class='time'>&nbsp;</td>
							    <td width='180'   class='c_title text_right' style='vertical-align:bottom;'>$Qty</td>
							    <td width='15'  >&nbsp;</td>
							     <td width='100'    $boxStyle><span style='margin-bottom:3px;display:block;'>$Boxs<span></td>
							    <td width='335'  class='c_title text_right' rowspan='2'>¥$Amount</td>
							    <td width='15'  rowspan='2'>&nbsp;</td>
						   </tr>
						    <tr height='25%'>
						         <td width='55'     class='time' ><span style='padding-top:6px;display:block;'>$OverIMG</span></td>
						         <td width='230'   class='c_title text_right'><span style='color:#FF0000;'>$OverQty</span></td>
						          <td width='15'  >&nbsp;</td>
							     <td width='80'    $boxStyle><span style='margin-bottom:8px;display:block;'>$OverBoxs<span></td>
						   </tr>
						   </table>";
           }
           else{
		        $ListSTR.="<table id='ListTable$m' name='ListTable[]' border='0' cellpadding='0' cellspacing='0' height='185px'>
						   <tr>
						        <td width='380'   class='c_title'>&nbsp;$Forshort<div $staffStyle>$StaffName</div></td>
						        <td width='55'     class='time'>&nbsp;</td>
							    <td width='180'   class='c_title text_right'>$Qty</td>
							    <td width='15'  >&nbsp;</td>
							     <td width='100'    $boxStyle><span style='margin-bottom:65px;display:block;'>$Boxs<span></td>
							    <td width='335'  class='c_title text_right'>¥$Amount</td>
							    <td width='15'  >&nbsp;</td>
						   </tr>
						   </table>";
			}	
			*/
				$m++;
		}
		$TotalCount++;		   
 }
 
for ($i=0;$i<count($ListArray);$i++){
	  $subArray=$ListArray[$i];
	  $PreAmount=round($subArray["Amount"]/$TotalAmount*100);
	  $PreAmount=$PreAmount>0?$PreAmount . "%":"";
	  $Forshort=$subArray['Forshort'];
	  $staffStyle=$subArray['staffStyle'];
	  $StaffName=$subArray['StaffName'];
	  $Qty=number_format($subArray['Qty']);
	  $Boxs=$subArray['Boxs'];
	  $Amount=number_format($subArray['Amount']);
	  $OverIMG=$subArray['OverIMG'];
	  $OverQty=number_format($subArray['OverQty']);
	  $OverBoxs=$subArray['OverBoxs'];
	  if ($subArray["OverCount"]>0)
	  {
	           $ListSTR.="<table id='ListTable$i' name='ListTable[]' border='0' cellpadding='0' cellspacing='0' height='185px' style='word-break: keep-all;
white-space:nowrap;'>
						   <tr height='75%'>
						        <td width='380'   class='c_title' rowspan='2'>&nbsp;$Forshort<div $staffStyle>$StaffName&nbsp;&nbsp;$PreAmount</div></td>
						        <td width='55'     class='time'>&nbsp;</td>
							    <td width='180'   class='c_title text_right' style='vertical-align:bottom;'>$Qty</td>
							    <td width='15'  >&nbsp;</td>
							     <td width='100'    $boxStyle><span style='margin-bottom:3px;display:block;'>$Boxs<span></td>
							    <td width='335'  class='c_title text_right' rowspan='2'>¥$Amount</td>
							    <td width='15'  rowspan='2'>&nbsp;</td>
						   </tr>
						    <tr height='25%'>
						         <td width='55'     class='time' ><span style='padding-top:6px;display:block;'>$OverIMG</span></td>
						         <td width='230'   class='c_title text_right'><span style='color:#FF0000;'>$OverQty</span></td>
						          <td width='15'  >&nbsp;</td>
							     <td width='80'    $boxStyle><span style='margin-bottom:8px;display:block;'>$OverBoxs<span></td>
						   </tr>
						   </table>";
           }
           else{
		        $ListSTR.="<table id='ListTable$i' name='ListTable[]' border='0' cellpadding='0' cellspacing='0' height='185px'>
						   <tr>
						        <td width='380'   class='c_title'>&nbsp;$Forshort<div $staffStyle>$StaffName&nbsp;&nbsp;$PreAmount</div></td>
						        <td width='55'     class='time'>&nbsp;</td>
							    <td width='180'   class='c_title text_right'>$Qty</td>
							    <td width='15'  >&nbsp;</td>
							     <td width='100'    $boxStyle><span style='margin-bottom:65px;display:block;'>$Boxs<span></td>
							    <td width='335'  class='c_title text_right'>¥$Amount</td>
							    <td width='15'  >&nbsp;</td>
						   </tr>
						   </table>";
			}	
}
 //本月出货
 $chResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS ShipQty FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid WHERE DATE_FORMAT(M.Date,'%Y-%m')=DATE_FORMAT(CURDATE(),'%Y-%m')",$link_id));
$MonthShipQty=$chResult["ShipQty"]==""?0:number_format($chResult["ShipQty"]);

$TotalQty=number_format($TotalQty);
$TotalShipQty=number_format($TotalShipQty);
$TotalOverQty=number_format($TotalOverQty);
$TotalAmount=number_format($TotalAmount);

$WeekName=substr($curWeek, 4,2);
 include "../iphoneAPI/subprogram/worktime_read.php";
 $upTime=date("H:i:s");
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 
<div id='headdiv' style='height:260px;'>
   <div id='weekdiv' class='float_left'><?php echo $WeekName; ?></div>
   <ul id='quantity3'  style='padding-top:35px;'>
            <li class='text_right'><span><?php echo $MonthShipQty; ?></span> </li>
	        <li class='text_left'><?php echo $TotalQty; ?><div></div></li>
   </ul>
   <ul id='count' class='border3'>
           <li style='width:540px;'>待出≥5d<div></div><br><span class='red_color'><?php echo $TotalOverQty; ?> </span><span><?php echo  "($TotalOverCount)"; ?> </span></li>
	       <li style='width:540px;'>待出金额 <br><span>¥<?php echo $TotalAmount; ?></span></li>
	 </ul>
</div>
<div id='listdiv' style='overflow: hidden;height:1690px;width:1080px;'>
<?php echo $ListSTR;?>
</div>