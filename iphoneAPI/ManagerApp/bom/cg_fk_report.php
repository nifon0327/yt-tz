<?php 
 //未付供应商货款
 //include "../../../basic/parameter.inc";
//$jsonArray = array(); 
$SearchRows=$ActionId=="NoPay"?" S.Estate=3 "	:" (S.Estate=3 OR S.Estate=0) ";
$subMonth=5;//统计月份数量
$CheckMonth=date("Y-m");		//当前月第一天
 $sub_M=$subMonth-1;
 $StratMonth=date("Y-m",strtotime("$CheckMonth -$sub_M month"));//计算的起始日期
 
 $MonthArray=array();
 for($i=0;$i<$subMonth;$i++){
      $tmpMonth=date("Y-m",strtotime(" $StratMonth +$i month"));
      $MonthArray[]=$tmpMonth;
      $sumMonth[$tmpMonth]=0;
      $DataArray[$tmpMonth]=array();
 }

 $ForshortArray=array();
$SumArray=array();
$djArray=array();
//读取全部未付货款
$AllResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount
FROM $DataIn.cw1_fkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE $SearchRows AND Amount>0 ",$link_id));
$AllAmount=sprintf("%.0f",$AllResult["Amount"]);

//读取未付货款
$ShipResult = mysql_query("SELECT SUM(S.Amount*C.Rate) AS Amount,S.CompanyId,P.Forshort,P.Letter,P.Id
FROM $DataIn.cw1_fkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE $SearchRows AND Amount>0 
GROUP BY S.CompanyId ORDER BY Amount DESC",$link_id);
$TotalAmount=0;$djTotalAmount=0; $NoCdjTotalAmount=0;
if ($ShipRow = mysql_fetch_array($ShipResult)) {
	$i=1;
	do{
		$Letter=$ShipRow["Letter"];
		$CompanyId=$ShipRow["CompanyId"];
		$Forshort=$ShipRow["Forshort"];
		$Amount=sprintf("%.0f",$ShipRow["Amount"]);
		$RMBAmount=number_format($Amount);
		$mAmount=array();
		$MonthResult= mysql_query("SELECT S.Month,SUM(S.Amount*C.Rate) AS Amount ,SUM(S.Amount) AS NoCAmount,C.preChar 
FROM $DataIn.cw1_fkoutsheet S
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE $SearchRows AND Amount>0 AND S.CompanyId='$CompanyId' GROUP BY S.Month ORDER BY S.Month",$link_id);
	 while($MonthRow=mysql_fetch_array($MonthResult)){
		    $Month=$MonthRow["Month"];
			$preChar=$MonthRow["preChar"];

			if($preChar!="¥") {
				$mAmount[$Month]=$MonthRow["NoCAmount"];
				$mPrechar[$Month]="$preChar";
			}
			else {
		    	$mAmount[$Month]=$MonthRow["Amount"];
				$mPrechar[$Month]="";
			}
			$sumMonth[$Month]+=$MonthRow["Amount"];
	}
	
	//已付订金
	$checkDjResult=mysql_fetch_array(mysql_query("SELECT SUM(-1*S.Amount*C.Rate) AS Amount ,SUM(-1*S.Amount) AS NoCAmount,C.preChar  
	FROM $DataIn.cw2_fkdjsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
    LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	 WHERE S.CompanyId='$CompanyId' and S.Did='0' and S.Estate=0 ",$link_id));
  	  
	  $djpreChar=$checkDjResult["preChar"];
	  $NoCdjAmount=$checkDjResult["NoCAmount"];
	  $djAmount=$checkDjResult["Amount"];
	  if($djpreChar!="¥") { 
		$NoCdjTotalAmount+= $djAmount;
	  }
	  else {
		  $djpreChar="";
	  }
  
$djTotalAmount+= $djAmount;
$Amount+= $djAmount;
//百分比
$TempPC=$AllAmount!=0?($Amount/$AllAmount)*100:0;
//$TempPC=$TempPC>=1?"<span class='greenB'>".(round($TempPC)."%")."</span>":"&nbsp;";
		
 $TotalAmount+=$Amount;
$Amount=number_format($Amount);


$ForshortArray[]=$Forshort;
for($i=0;$i<$subMonth;$i++){
      $M=$MonthArray[$i];
      $DataArray[$M][]=$mAmount[$M]==""?"":$preChar. number_format($mAmount[$M]);
 }
 
$SumArray[]="¥" .$Amount;
$djArray[]=$djAmount<0?$preChar. number_format($djAmount):"";
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

$MonthArray[]="已付订金";
$MonthArray[]="合计RMB";
array_unshift($MonthArray, "供应商");
$jsonArray=array($MonthArray,$tempArray);

//echo json_encode($jsonArray);
?>