<?php
     //$Line=$Line==""?"C":$Line;
      include_once "tasks_function.php";
      include "../basic/parameter.inc";
      
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
    $curWeek=$dateResult["curWeek"];
   
     $SC_TYPE='7100';
     $checkResult=mysql_fetch_array(mysql_query("SELECT POrderId FROM $DataIn.sc_currentmission WHERE LineNumber='$Line' ORDER BY Id DESC LIMIT 1",$link_id));
     $CurPOrderId=$checkResult["POrderId"];
     
      $myResult = mysql_query(" SELECT A.GroupId,A.POrderId,A.BlDate,A.llDate,A.ScQty,IFNULL(A.ScDate,Now()) AS ScDate,S.Qty,S.sgRemark,M.OrderPO,C.Forshort,P.cName,SC.DateTime,IF(SC.Id>0,1,0) AS LineSign,A.llEstate,
      YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks,MAX(SM.DateTime) AS mDateTime,S.ShipType  
      FROM(
               SELECT S1.*,IFNULL(SUM(C.Qty),0) AS ScQty,MAX(C.Date) AS ScDate FROM (
				          SELECT S0.POrderId,S0.GroupId,Max(S0.BlDate) AS BlDate,Max(S0.llDate) AS llDate,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty,SUM(S0.llEstate) AS llEstate FROM (      
				             SELECT 
										S.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(SUM(L.Estate),0) AS llEstate,
										Max(BM.Date) AS BlDate,LG.GroupId,Max(CONCAT(LM.Date,' ',LM.Time)) AS llDate      
				                        FROM $DataIn.yw1_ordermain M
										INNER JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
										LEFT JOIN $DataIn.sc1_mission LS ON LS.POrderId=S.POrderId 
										LEFT JOIN $DataIn.staffgroup LG ON LG.Id=LS.Operator 
										LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				                        LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
				                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
                                        LEFT JOIN $DataIn.yw9_blmain BM ON L.Pid=BM.Id
                                        LEFT JOIN $DataIn.ck5_llmain LM ON L.Mid=LM.Id 
                                         LEFT JOIN $DataIn.stuffproperty T  ON T.StuffId=G.StuffId AND  T.Property='8' 
				                        WHERE  S.scFrom>0 AND S.Estate=1 AND G.Id>0 AND SUBSTR(LG.GroupName,-1,1)='$Line' AND ST.mainType<2   AND T.StuffId IS NULL 
				                         GROUP BY G.StockId 
				               )S0 GROUP BY S0.POrderId 
				     )S1 
     LEFT JOIN $DataIn.sc1_cjtj C ON C.POrderId=S1.POrderId  AND C.TypeId='$SC_TYPE' 
     WHERE S1.blQty=S1.llQty OR C.Id>0  GROUP BY S1.POrderId 
     )A 
    INNER JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId  
	INNER JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	INNER JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
	LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
	LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
	LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId
	LEFT JOIN $DataIn.sc1_mission SC ON SC.POrderId=S.POrderId 
	LEFT JOIN $DataIn.sc_currentmission SM ON SM.POrderId=S.POrderId 
	GROUP BY S.POrderId ORDER BY ScDate,mDateTime DESC,Weeks,BlDate" ,$link_id); 

$today=date("Y-m-d H:i:s");

$SumQty=0;$SumScQty=0;
$OweQty=0;$OweCount=0;
$WaitQty=0;$WaitCount=0;
$ListSTR="";$m=0;	
$TotalCount=0;	$FinishCount=0;
while($myRow = mysql_fetch_assoc($myResult)){
      $GroupId=$myRow["GroupId"];
	  $POrderId=$myRow["POrderId"];
	  $Week1=substr($myRow["Weeks"], 4,1);
	  $Week2=substr($myRow["Weeks"], 5,1);
	  $WeekColor=$curWeek>$myRow["Weeks"]?'bgcolor_red':'bgcolor_black';
	  $Forshort=$myRow["Forshort"];
	  $cName=$myRow["cName"];
	  $OrderPO=$myRow["OrderPO"];
	  $Qty=$myRow["Qty"];
	  $ScQty=$myRow["ScQty"];
	 	  //欠尾数
	  $ScDate=$myRow["ScDate"];
	  $ScDateColor="";$ScDateStr="";
	  $scMinute=(strtotime($today)-strtotime($ScDate))/60;
	  if ($scMinute>30){
		  $OweQty+=$Qty-$ScQty;
		  $OweCount++;
		  $ScDateStr=GetDateTimeOutString($ScDate,'');
		  $tableClass=" tb_bgcolor1 ";
		  $FinishCount+=$scMinute<60 && $myRow["sgRemark"]==""?1:0;
		  $ScDateColor=$scMinute<60?"orange_color":" red_color ";
	  }
	  else{
		   //生产中
		  if ($ScQty>0){
		          $SumQty+=$Qty; 
		          $SumScQty+=$ScQty;
		          $tableClass=" tb_bgcolor2";
		  }
		  else{
			  $WaitQty+=$Qty;
			  $WaitCount++;
			   $tableClass=" tb_bgcolor0";
		  }
	  }
	  
	  $TotalCount++;
	  $TotalQty+=$Qty-$ScQty;
		 
	  $BlDate=$myRow["llDate"]=="" || $myRow["llEstate"]>0?$myRow["BlDate"]:$myRow["llDate"];
	  $DateChars=$myRow["llDate"]=="" || $myRow["llEstate"]>0?"占":"备";
	  //$DateChars="占";
	  //$PackRemark=str_replace("(拆分的订单)", "", $myRow["PackRemark"]);
	  $sgRemark="";
	  if ($DateChars=="占"){
		  $blDateStr=GetDateTimeOutString($BlDate,$myRow["DateTime"]);
		  $blDateStr=str_replace("前", "", $blDateStr);
		  $blDateChars=$DateChars;
		  
		  $DateChars="配";
		  $DateStr=GetDateTimeOutString($myRow["DateTime"],'');
		  $blHours=(strtotime($today)-strtotime($myRow["DateTime"]))/3600;
		  $blColors=$blHours>24?"red_color":"";
		  
	  }
	  else{
		  $sgRemark=$myRow["sgRemark"];
		  $DateStr=GetDateTimeOutString($BlDate,'');
		  $blHours=(strtotime($today)-strtotime($BlDate))/3600;
		  $blColors=$blHours>24?"red_color":"";
		  $blDateStr="";$blDateStr="";
	  }

	  $curSign=$CurPOrderId==$POrderId?"<img src='image/qr.png' width='42' style='float:right;margin:10px 10px 0px 0px;'/>":"";
	  $tableClass=$CurPOrderId==$POrderId?" tb_bgcolor2":$tableClass;
	   
	  $Qty=number_format($Qty);
	  $ScQty=number_format($ScQty);
	  if ($m<10){
	             //出货方式
		       $ShipType=$myRow["ShipType"];
		      $ShipType=$ShipType===""?"":"<image src='../images/ship$ShipType.png' style='float:right;margin:10px 10px 0px 0px;width:48px;height:48px;'/>";
	    
				  $ListSTR.="<table id='ListTable$m' name='ListTable[]' class='$tableClass'>
					<tr>
					    <td rowspan='2' width='120' class='week $WeekColor'><div>$Week1</div><div>$Week2</div></td>
					    <td colspan='4' width='960' class='title'><span>$Forshort-</span>$cName $ShipType $curSign</td>
				   </tr>
				   <tr>
					    <td width='240'>$OrderPO</td>
					    <td width='220' class='qty'><img src='image/order.png'/>$Qty</td>
					    <td width='220' class='qty'><img src='image/register.png'/>$ScQty</td>
					    <td width='280' class='time $blColors'>$DateStr<div>$DateChars</div></td>
				   </tr>";
				   
				   if ($sgRemark!=""){
					   $ListSTR.="<tr>
									        <td  class='remark_icon'><img src='image/remark.png'/></td>
									        <td colspan='3' class='remark'>$sgRemark</td>
									         <td  class='remark text_right'><span class='$ScDateColor'>$ScDateStr</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								      </tr>";
				   }
				   else{
				     if ($DateChars=="配"){
					       $ListSTR.="<tr>
									        <td  class='remark_icon'>&nbsp;</td>
									        <td colspan='3' class='remark'>&nbsp;</td>
									          <td  class='time'>$blDateStr<div>$blDateChars</div></td>
								      </tr>";
				     }
				     else{
					   $ListSTR.="<tr>
									        <td  class='remark_icon'>&nbsp;</td>
									        <td colspan='3' class='remark'>&nbsp;</td>
									          <td  class='remark text_right'><span class='$ScDateColor'>$ScDateStr</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								      </tr>";
					  }
				   }
				 $ListSTR.="</table>";
	 }
	 $m++;
}

if ($ListSTR!=""){
	$curDate=date("Y-m-d");
	//组装总人数
	 $BranchResult =mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts   
			FROM $DataIn.staffgroup G 
			LEFT JOIN $DataIn.sc1_memberset  S ON S.GroupId=G.GroupId AND S.Date='$curDate' 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
			WHERE  G.TypeId='$SC_TYPE'  AND M.Estate=1 AND M.cSign=7 ",$link_id));
	$BranchNums=$BranchResult["Counts"];	
	
	//当前人数
	 $GroupResult =mysql_query("SELECT M.Number,M.Name,SUM(IF(C.Id>0,1,0)) AS Counts   
			FROM $DataIn.staffgroup G 
			LEFT JOIN $DataIn.sc1_memberset  S ON S.GroupId=G.GroupId AND S.Date='$curDate' 
			LEFT JOIN $DataIn.checkinout C ON C.Number=S.Number AND  DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$curDate' AND C.CheckType='I'  
			LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader
			WHERE  G.GroupId='$GroupId' AND M.Estate=1 AND M.cSign=7 ",$link_id);
	 if ($GroupRow = mysql_fetch_array($GroupResult)) {
	     $LeaderNumber=$GroupRow["Number"];
		 $LeaderName=$GroupRow["Name"];
		 $GroupNums=$GroupRow["Counts"];
	 }
	 
	 //请假人数
	 $OverTime=date("Y-m-d") . " 17:00:00";
	 $LeaveResult =mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts  FROM (SELECT K.Number   
			FROM $DataPublic.kqqjsheet K
			LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
			WHERE (K.StartDate<NOW() AND (K.EndDate>=NOW() OR K.EndDate>='$OverTime')) AND M.GroupId='$GroupId' AND M.cSign=7 AND M.Estate=1  GROUP BY K.Number)A ",$link_id));
	$LeaveNums=$LeaveResult["Counts"];
	
	//今日生产数量
	/*
	$ScedResult =mysql_fetch_array(mysql_query("SELECT SUM(A.ScQty) AS ScQty,SUM(A.OrderQty) AS OrderQty 
	FROM (
	SELECT SUM(S.Qty) AS ScQty,0 AS OrderQty     
			FROM $DataIn.sc1_cjtj S 
			WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND S.GroupId='$GroupId' 
	UNION ALL
	        SELECT 0 AS ScQty,Y.Qty AS OrderQty 
	        FROM $DataIn.sc1_cjtj S 
	        LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
	        WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND S.GroupId='$GroupId' GROUP BY Y.POrderId 
	)A",$link_id));
	*/
	
	$ScedResult =mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS ScQty,0 AS OrderQty  FROM $DataIn.sc1_cjtj S  
			WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND S.boxId like '$Line%'  
",$link_id));//AND S.GroupId='$GroupId' 
	
	$TotalScQty=$ScedResult["ScQty"]==""?0:$ScedResult["ScQty"];
	
	//$TotalQty=$ScedResult["OrderQty"]==""?0:$ScedResult["OrderQty"];
	//$TotalQty+=$WaitQty;
	
	$TotalScQty=number_format($TotalScQty);
	$TotalQty=number_format($TotalQty);
	
	$OweQty=number_format($OweQty) . "($OweCount)";
	$WaitQty=number_format($WaitQty) . "($WaitCount)";
	
	$AlignClass=$Align=="R"?"float_right":"float_left";
	$LeaderClass=$Align=="R"?"leader_right":"leader_left";
	$MarginClass=$Align=="R"?"margin_right_15":"margin_left_15";
	
	$GroupClass=$Align=="R"?"float_left margin_left":"float_right";
	$ClearClass=$Align=="R"?"clear_left":"clear_right";
	
	 include "../iphoneAPI/subprogram/worktime_read.php";
     $upTime=date("H:i:s");

     //echo $upTime;
?>
 <input type='hidden' id='workTime' name='workTime' value='<?php echo $workTimes; ?>'>
 <input type='hidden' id='curTime' name='curTime' value='<?php echo $upTime; ?>'>
 <input type='hidden' id='TotalCount' name='TotalCount' value='<?php echo $TotalCount; ?>'>
 <input type='hidden' id='FinishCount' name='FinishCount' value='<?php echo $FinishCount; ?>'>
 
<div id='headdiv'>
	 <div id='linediv' class='<?php echo $AlignClass?>'><?php echo $Line; ?></div>
	 <img id='leader'  class='<?php echo $AlignClass . " " . $MarginClass;?>'  src='photo/<?php echo $LeaderNumber; ?>.png'/>  
	 <div  id='leader_name'  class='<?php echo  $LeaderClass?>' ><span><?php echo $LeaderName; ?></span></div>
	 <ul id='group' class='<?php echo  $GroupClass?>'>
	      <li><img src='image/group_staff.png'/><?php echo $BranchNums; ?>人</li>
	      <li><img src='image/working_staff.png'/><?php echo $GroupNums; ?>人</li>
	      <li><img src='image/leave_staff.png' style='margin-top:3px;'/><?php echo $LeaveNums; ?>人</li>
	 </ul>
	 <ul id='quantity' class='<?php echo  $GroupClass . " " . $ClearClass;?>'>
	    <?php if ($Align=="R"){ ?>
	        <li class='text_right'><?php echo $TotalQty; ?> <div></div></li>
	        <li class='text_left'><span><?php echo $TotalScQty; ?></span></li>
	     <?php }else {?>
	         <li class='text_right'><span><?php echo $TotalScQty; ?> </span><div></div></li>
	         <li class='text_left'><?php echo $TotalQty; ?></li>
	     <?php }?>
	 </ul>
	 <ul id='count'>
	      <li>欠尾数 <div></div><span><br><?php echo $OweQty; ?> </span></li>
	      <li>生产中 <div></div><span><br><span id='register'><?php echo $SumScQty; ?></span>/<?php echo $SumQty; ?></span></li>
	      <li>待生产 <span><br><?php echo $WaitQty; ?></span></li>
	 </ul>
</div>
<div>
<?php echo $ListSTR;?>
</div>
<?php } ?>