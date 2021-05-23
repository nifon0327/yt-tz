<?php 

$Id = $categoryId;

$Value_Y = array();
unset($Value_Y);
$Value_W = array();
unset($Value_W);
$Value_A = array();
unset($Value_A);

$subTotalList = array();
$monthStrList = array();

$monthCount = $MonthCount;
$checkMonth = "";
$NowMonth = $checkMonth == "" ? date("Y-m-01") : $checkMonth."-01";	//起始月份：默认为当前月

//汇率参数
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];	
	}
	while($rateRow = mysql_fetch_array($rateResult));
}

for($Subscript = 0 ; $Subscript < $monthCount ; $Subscript++) {
	
	$StepM = $Subscript;
	$CheckTime = date("Y-m",strtotime("$NowMonth -$StepM month"));
	$TempPayDatetj=" AND DATE_FORMAT(M.PayDate,'%Y-%m')='$CheckTime'";
	$TempDatetj=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
	$TempMonthtj="  AND M.Month='$CheckTime'";
	$TempSendDatetj=" AND DATE_FORMAT(M.SendDate,'%Y-%m')='$CheckTime'";
	$TempqkDatetj=" AND DATE_FORMAT(M.qkDate,'%Y-%m')='$CheckTime'";
	$TempDateTax=" AND DATE_FORMAT(M.TaxDate,'%Y-%m')='$CheckTime'";
    $TempDateModelf=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
	$TempDeliveryDate=" AND DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$CheckTime'";

	//資料初始化
	if ($Id == '3' || $Id == '4' || $Id == '5' || $Id == '8') {
	
		//行政费用初始化
		$checkHZDSql=mysql_query("SELECT concat('HZ',S.TypeId) AS Name FROM $DataPublic.adminitype S WHERE 1 ORDER BY S.TypeId",$link_id);
		if($checkHZDRow=mysql_fetch_array($checkHZDSql)) {
			do {
				$NameA=$checkHZDRow["Name"]."_A";$NameY=$checkHZDRow["Name"]."_Y";$NameW=$checkHZDRow["Name"]."_W";
				$TempHZA=strval($NameA); $TempHZY=strval($NameY);$TempHZW=strval($NameW);
				$$TempHZA=0;$$TempHZY=0;$$TempHZW=0;
			}
			while($checkHZDRow=mysql_fetch_array($checkHZDSql));
		}
		
		//全部行政费用
		$checkHZSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('HZ',M.TypeId,'_A') AS Name FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Date>='2008-07-01' $TempDatetj AND (M.Estate=3 OR M.Estate=0)  GROUP BY M.TypeId ORDER BY M.TypeId",$link_id);
		if($checkHZRow=mysql_fetch_array($checkHZSql)){
			do{
				$Amount=sprintf("%.0f",$checkHZRow["Amount"]);
				$Name=$checkHZRow["Name"];
				$TempHZ=strval($Name); 
				$$TempHZ=$Amount;
			}
			while($checkHZRow=mysql_fetch_array($checkHZSql));
		}
	}
	else if ($Id == '10') {
		//非BOM费用初始化
		$checkNONBOMSql=mysql_query("SELECT concat('NONBOM',Id) AS Name FROM $DataPublic.nonbom1_maintype ORDER BY Id",$link_id);
		if($checkNONBOMRow=mysql_fetch_array($checkNONBOMSql)){
			do{
				$NameA=$checkNONBOMRow["Name"]."_A";$NameY=$checkNONBOMRow["Name"]."_Y";$NameW=$checkNONBOMRow["Name"]."_W";
				$TempNONBOMA=strval($NameA); 
				$TempNONBOMY=strval($NameY);
				$TempNONBOMW=strval($NameW);
				$$TempNONBOMA=0;
				$$TempNONBOMY=0;
				$$TempNONBOMW=0;
				}while($checkNONBOMRow=mysql_fetch_array($checkNONBOMSql));
		}
	}
	
	include "./../../syb/subprogram/new_desk_syb_".$Id.".php";
	  
	$monthStrList[] = $CheckTime;
}

//把Value_A的資料結構反轉成subTotalList的結構：子項目>>月份1、月份2、月份3…
$checkSubSql = mysql_query("SELECT * FROM $DataPublic.sys8_pandlsheet WHERE Mid='$Id' AND Estate=1 ORDER BY SortId",$link_id);
if($checkRow = mysql_fetch_array($checkSubSql)) {
	
	$categoryIndex = 0;
	$categorySubTotal = array();
	
	do {
		
		$categoryCode = $checkRow["AjaxNo"];
		$parameter = $checkRow["Parameters"];
		
		if (strlen($parameter) > 0) {
			$categoryCode .= "_".$parameter;
		}
		
		for ($monthIndex = 0; $monthIndex < count($Value_A); $monthIndex ++) {
			
			$monthString = $monthStrList[$monthIndex];
			$subTotalList[$categoryCode][$monthString] = $Value_A[$monthIndex][$categoryIndex];	

		}
		
		$categoryIndex ++;
	}
	while($checkRow = mysql_fetch_array($checkSubSql));
}

/* var_dump($subTotalList, "<br>"); */
$monthSubTotal = $subTotalList[$ItemMid];

//101 跟 101_debit 都一樣從list101.php 進去，但多傳入一個parameter
$itemArr = explode("_", "$ItemMid");

if (count($itemArr) > 1) {
	$ItemMid = $itemArr[0];
	$itemParameter = $itemArr[1];
}

$listFileName = $ItemMid;
if ($ItemMid == "0") {
	$listFileName = $Id;
}
//if ($LoginNumber==10868) echo "report/syb_report_subfunction/list_".$listFileName.".php";

include "report/syb_report_subfunction/list_".$listFileName.".php";

/* $jsonArray = $Value_A; */
/* $jsonArray = $subTotalList; */
/* $jsonArray = $monthSubTotal; */

/*
 以下為syb需要用到的方法，從 ..\model\modelfunction.php 搬移過來
*/
function zerotospace($STR) {
	if($STR==0) {
		return "&nbsp;";
	}
	else {
		return $STR;
	}
}

function del0($s) {  
    $s = trim(strval($s));  
    if (preg_match('#^-?\d+?\.0+$#', $s)) {  
        return preg_replace('#^(-?\d+?)\.0+$#','$1',$s);  
    }   
    if (preg_match('#^-?\d+?\.[0-9]+?0+$#', $s)) {  
        return preg_replace('#^(-?\d+\.[0-9]+?)0+$#','$1',$s);  
    }  
    return $s;  
}

function SpaceValue0($STR) {
	if($STR==0) {
		return "&nbsp;";
	}
	else {
		return floor($STR);
	}
}
/* syb方法結束 */

?>