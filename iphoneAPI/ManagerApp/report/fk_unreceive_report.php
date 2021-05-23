<?php 
 //未收货款
// include "../subprogram/myfunction.php";
// include "../../../basic/parameter.inc";
//$jsonArray = array(); 
//$subMonth=7;//统计月份数量
//$CheckMonth=date("Y-m");		//当前月第一天

$MonthArray=array();
$MResult=mysql_query("SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
        WHERE M.Estate=0 AND M.cwSign IN (1,2) 
        GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY DATE_FORMAT(M.Date,'%Y-%m')",$link_id);
while($MRow=mysql_fetch_array($MResult)){
       $tmpMonth=$MRow["Month"];
       $MonthArray[]=$tmpMonth;
       $sumMonth[$tmpMonth]=0;
       $DataArray[$tmpMonth]=array();
}

$subMonth=count($MonthArray);

$ForshortArray=array();
$SumArray=array();
$djArray=array();
$PercentArray=array();

//未收货款总额
$AllResult = mysql_fetch_array(mysql_query("SELECT SUM(A.Amount) AS GatheringSUM FROM(
       SELECT SUM( S.Price * S.Qty*D.Rate* M.Sign ) AS Amount 
            FROM  `$DataIn`.`ch1_shipmain` M
            LEFT JOIN  `$DataIn`.`ch1_shipsheet` S ON S.Mid = M.Id
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
            LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
          WHERE M.Estate =0 AND M.cwSign IN ( 1, 2 ) 
       UNION ALL
        SELECT IFNULL(SUM(-P.Amount*D.Rate),0) AS Amount 
            FROM $DataIn.cw6_orderinsheet P
            LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
             LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
            LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
            WHERE M.cwSign='2'
       UNION ALL
                SELECT IFNULL(SUM(-P.Amount*D.Rate),0) AS Amount  
                FROM $DataIn.cw6_advancesreceived  P 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId = P.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
             WHERE  P.Mid='0')A
        ",$link_id));
$AllAmount=sprintf("%.0f",$AllResult["GatheringSUM"]); 



//读取未收货款
$ShipResult = mysql_query("SELECT * FROM (SELECT SUM( S.Price*S.Qty*M.Sign) AS Amount,M.CompanyId,C.Forshort,C.Currency,C.PayMode,D.Rate,D.preChar,SUM( S.Price*S.Qty*M.Sign*D.Rate) AS RMBAmount 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
WHERE M.Estate =0 AND M.cwSign IN (1,2) GROUP BY M.CompanyId 
UNION ALL 
SELECT 0 AS Amount,M.CompanyId,C.Forshort,C.Currency,C.PayMode,D.Rate,D.preChar,0 AS RMBAmount 
FROM $DataIn.cw6_advancesreceived M
LEFT JOIN $DataIn.trade_object C ON  M.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.currencydata D   ON C.Currency=D.Id
WHERE M.Mid=0 GROUP BY CompanyId)A  
GROUP BY A.CompanyId  ORDER BY RMBAmount  DESC",$link_id);

$TotalAmount=0;$djTotalAmount=0; $NoCdjTotalAmount=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$ShipRow["Forshort"];
		$Amount=sprintf("%.0f",$ShipRow["Amount"]);
		$RMBAmount=sprintf("%.0f",$ShipRow["RMBAmount"]);
		$preChar=$ShipRow["preChar"];
		$PayMode=$ShipRow["PayMode"];
		$Rate=$ShipRow["Rate"];
		
		$mAmount=array();$SumPayed=0;
	    $MonthResult= mysql_query("SELECT SUM(S.Price*S.Qty*M.Sign) AS Amount,DATE_FORMAT(M.Date,'%Y-%m') AS Month,D.preChar 
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
        WHERE M.Estate=0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' 
        GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
	 while($MonthRow=mysql_fetch_array($MonthResult)){
		    $Month=$MonthRow["Month"];
		    
		    //部分收款
			$CheckPart1=mysql_fetch_array(mysql_query("SELECT SUM(P.Amount*M.Sign) AS GatheringSUM 
			FROM $DataIn.cw6_orderinsheet P
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
			LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
			WHERE M.cwSign='2' AND M.CompanyId='$CompanyId'  AND  DATE_FORMAT(M.Date,'%Y-%m')='$Month'",$link_id));
			$PayedAmount=$CheckPart1["GatheringSUM"]==""?0:$CheckPart1["GatheringSUM"];
		    
		    $mAmount[$Month]=$MonthRow["Amount"]-$PayedAmount;
			$sumMonth[$Month]+= $mAmount[$Month]*$Rate;
			$SumPayed+=$PayedAmount;
	 }
	 

      //预收货款
		$CheckPreJY=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) FK_JY FROM $DataIn.cw6_advancesreceived WHERE CompanyId='$CompanyId' AND Mid='0'",$link_id));
		$djAmount=$CheckPreJY["FK_JY"]==""?0:$CheckPreJY["FK_JY"];
	
  
	$djTotalAmount+= $djAmount*$Rate;
	$Amount-= $djAmount+$SumPayed;
	$RMBAmount=$Amount*$Rate;
	//百分比
	$Percent=$AllAmount!=0?($RMBAmount/$AllAmount)*100:0;
	$Percent=$Percent>=1?round($Percent)."%|#207155":"";
	$PercentArray[]=$Percent;
			
	 $TotalAmount+=$RMBAmount;
	$Amount=number_format($Amount);

    //供应商|字体颜色|背影颜色|右上角图标
    $ForshortArray[]=$Forshort . "|||paymode_$PayMode";

	for($i=0;$i<$subMonth;$i++){
	      $M=$MonthArray[$i];
	      $TextColor=$mAmount[$M]*$Rate>=500000?"|#FF0000":"";
	      $TextColor=$mAmount[$M]*$Rate>=1000000?"|#FF8100":$TextColor;
	      $DataArray[$M][]=$mAmount[$M]==""?"":$preChar. number_format($mAmount[$M]) .$TextColor;
	 }
 
	$SumArray[]=$preChar .$Amount;
	$djArray[]=$djAmount>0?$preChar. number_format($djAmount) . "|#2C8F66":"";//|over_time
}while ($ShipRow = mysql_fetch_array($ShipResult));
}
$tempArray=array();
$ForshortArray[]="合     计:";

$tempArray[]=$ForshortArray;

for($i=0;$i<$subMonth;$i++){
      $M=$MonthArray[$i];
      $DataArray[$M][]="¥" . number_format($sumMonth[$M]);
      $tempArray[]=$DataArray[$M];
 }
$SumArray[]="¥" .number_format($TotalAmount);
$djArray[]="¥" .number_format($djTotalAmount);

$tempArray[]=$djArray;
$tempArray[]=$SumArray;

$MonthArray[]="预收货款";
$MonthArray[]="合       计";
$MonthArray[]="所占比例";
$tempArray[]=$PercentArray;
array_unshift($MonthArray, "客户");

//$NavTitle=$ActionId=="NoPay"?"未收货款":"货款";
$SelMonth="合       计";
$SetArray=array("NavTitle"=>"未收货款","ShowCol"=>"$SelMonth",
                             "LeftColWidth"=>"120","ColHeight"=>"25","ColWidth"=>"100",
                            "Title"=>array("Height"=>"30","Color"=>"","BgColor"=>"#CCCCCC","FontSize"=>"13"),
							"TextColor"=>"#000000","TextAlign"=>"R","FontSize"=>"13","BorderColor"=>"","SeparatorColor"=>"" 
							      );
							      
$jsonArray=array("SET"=>$SetArray,"ColTitle"=>$MonthArray,"ColData"=>$tempArray);

//echo json_encode($jsonArray);
?>