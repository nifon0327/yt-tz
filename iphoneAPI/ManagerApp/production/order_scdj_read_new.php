<?
// 新的今日组装页面 order_scdj_read_new
/*
// SQL 4 all monthes's quantities 所有月份的数量 
SELECT DATE_FORMAT(D.Date,'%Y-%m') AS Date,SUM(D.Qty) AS Qty FROM d7.sc1_cjtj D WHERE 1 AND D.TypeId='7100' GROUP BY DATE_FORMAT(D.Date,'%Y-%m') ORDER BY Date DESC




*/
$SearchRows=$dModuleId=="1111"?" AND D.TypeId<>'7100' ":" AND D.TypeId='7100' ";
if (count($info) > 2 && $info[1]=='xqAm' ) { 
	$monthCondi = $info[2];
	$singleMSql = mysql_query("SELECT sum(D.Qty*A.Price) as xqAM FROM $DataIn.sc1_cjtj D LEFT JOIN $DataIn.cg1_stocksheet C ON C.POrderId=D.POrderId LEFT JOIN $DataIn.stuffdata A ON A.StuffId=C.StuffId and A.TypeId=D.TypeId WHERE 1 $SearchRows  and DATE_FORMAT(D.Date,'%Y-%m')='$monthCondi'",$link_id);
	$xqAM = 0;
	if ($singleRow = mysql_fetch_array($singleMSql)) {
		$xqAM = $singleRow["xqAM"];
	}
	$xqAM = number_format(round($xqAM,0));
	
	//实际工资
	//$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Jbjj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk-$dkfl
	$amountSql = "select sum(d.Dx+d.Jbf+d.Gljt+d.Gwjt+d.Jj+d.Jbjj+d.Shbz+d.Zsbz+d.Jtbz+d.Yxbz+d.taxbz-d.Kqkk-d.dkfl) as AllAmount from $DataIn.cwxzsheet d where d.branchId=8 and d.Month='$monthCondi'";
	$allAmount = 0;
	if ($allAmountRow = mysql_fetch_array(mysql_query($amountSql, $link_id))) {
		$allAmount = number_format($allAmountRow["AllAmount"]);
	}
	$allAmount = $allAmount>0?"¥$allAmount":"";
	$jsonArray = array("xqAm"=>"¥$xqAM","AllAmount"=>$allAmount);
} else {
	
$today=date("Y-m");

$lastMonth = date('Y-m',strtotime('-1 month'));
$Layout=array( "Title"=>array("Frame"=>"40, 2, 230, 25"),
                          "Col2"=>array("Frame"=>"135,32,48, 15","Align"=>"L","Color"=>"#AAAAAA"),
                          "Col3"=>array("Frame"=>"215,32,48, 15","Align"=>"L","Color"=>"#AAAAAA")
                         );
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"125,35,8.5,10"),
                          "Col3"=>array("Name"=>"scdj_3","Frame"=>"200,35,8.5,10")
                          );
                          


$willJsonArray = array();
$jsonArray = array();
$sql = "SELECT DATE_FORMAT(D.Date,'%Y-%m') AS Date,SUM(D.Qty) AS QtyAll FROM $DataIn.sc1_cjtj D 

 WHERE 1   $SearchRows and DATE_FORMAT(D.Date,'%Y-%m')>'2013-01' GROUP BY DATE_FORMAT(D.Date,'%Y-%m') ORDER BY Date DESC";
$allMonthRs = mysql_query($sql, $link_id);
$count = 0;
while ($allMonthRow = mysql_fetch_array($allMonthRs)) {
	$monthStr = $allMonthRow["Date"];
	$needAM = "";
	$allAmount = "..";
	$allAmountRS = mysql_query("select d.needXZ,d.factXZ from $DataIn.buffer4analysis d where d.dateM='$monthStr'");
	
	if ($allAmountRow = mysql_fetch_array($allAmountRS)) {
		
		$needAM = $allAmountRow["needXZ"];
		$allAmount = $allAmountRow["factXZ"];
	} else {
		$singleMSql = mysql_query("SELECT sum(D.Qty*A.Price) as xqAM FROM $DataIn.sc1_cjtj D LEFT JOIN $DataIn.cg1_stocksheet C ON C.POrderId=D.POrderId LEFT JOIN $DataIn.stuffdata A ON A.StuffId=C.StuffId and A.TypeId=D.TypeId WHERE 1 $SearchRows  and DATE_FORMAT(D.Date,'%Y-%m')='$monthStr'",$link_id);
	$xqAM = 0;
	if ($singleRow = mysql_fetch_array($singleMSql)) {
		$xqAM = $singleRow["xqAM"];
	}
	$xqAM = number_format(round($xqAM,0));
	$needAM = $xqAM;
	
	if (strtotime($monthStr) <= strtotime("2013-01")) {
		break;
	}
	
	//实际工资
	//$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Jbjj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk-$dkfl
	$amountSql = "select sum(d.Dx+d.Jbf+d.Gljt+d.Gwjt+d.Jj+d.Jbjj+d.Shbz+d.Zsbz+d.Jtbz+d.Yxbz+d.taxbz-d.Kqkk-d.dkfl) as AllAmount from $DataIn.cwxzsheet d
	left join $DataIn.staffgroup p on p.groupid=d.groupid
	  where p.typeid>0 and p.estate!=0 and d.Month='$monthStr'";
	 
	
	 
	$allAmount = 0;
	if ($allAmountRow = mysql_fetch_array(mysql_query($amountSql, $link_id))) {
		$allAmount = ($allAmountRow["AllAmount"]);
	}
	
	 //社保公积金
	 	$CamountSql = "select sum(d.cAmount) as AllAmount from $DataIn.sbpaysheet d
	left join $DataPublic.staffmain m on m.number=d.number 
	left join $DataIn.staffgroup p on p.groupid=m.groupid 
		 where p.typeid>0 and p.estate!=0 and d.Month='$monthStr' and d.TypeId in (1,2)";
	 
	if ($SBAmountRow = mysql_fetch_array(mysql_query($CamountSql, $link_id))) {
		$allAmount += ($SBAmountRow["AllAmount"]);
	}
		
	 $allAmount = number_format($allAmount);
	
	$allAmount = $allAmount>0?"$allAmount":"";
	
		if ($today != $monthStr && $lastMonth != $monthStr) {
			mysql_query("insert into  $DataIn.buffer4analysis (factXZ,needXZ,dateM) values ('$allAmount','$xqAM','$monthStr')");
		}
	}
	
	$QtyAMonth = $allMonthRow["QtyAll"];
	//
	
	
	if (0 == $count) {
		$monthParam = $monthStr;  
		include "order_scdj_read_month.php";
		$dataArray = $jsonArray;
	} else
	 {
		$dataArray = array();
	}
	$count ++;
	
	$hasRemarkC = 0;
	/*
$hasRemark = mysql_query("select lg.id as counting from $DataIn.cz_scdj_log lg where DATE_FORMAT(lg.date,'%Y-%m') ='$monthStr' and lg.estate=1 and lg.typeid='1112zz' limit 0,1");
if ($hasRemarkR = mysql_fetch_array($hasRemark)) {
	$hasRemarkC = $hasRemarkR["counting"];
}
*/
	
	$willJsonArray[] = array("month"=>$monthStr,"Qty"=>number_format($QtyAMonth),"data"=>$dataArray,"xqAm"=>"¥$needAM","AllAmount"=>"¥$allAmount","HasRemark"=>$hasRemarkC ); 
}

$jsonArray = array("data"=>$willJsonArray, "Layout"=>$Layout, "IconSet"=>$IconSet, "Title"=>"今日组装");
}
?>