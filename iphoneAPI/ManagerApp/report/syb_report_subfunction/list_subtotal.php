<?php 
/* echo("$SelMonth, $ItemMid"); */



$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE  Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
$ChangeAction=$ChangeAction==""?6:$ChangeAction;//要显示的月份数：默认为6个月

/* $MonthCount=$ChangeAction; */
$SendValue="";$checkMonth="";

//检查可用的项目数
$checkItemNum=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums FROM $DataPublic.sys8_pandlsheet A LEFT JOIN $DataPublic.sys8_pandlmain B ON B.Id=A.Mid WHERE A.Estate=1 AND B.Estate=1",$link_id));
$ItemNums=$checkItemNum["Nums"];
//初始化数组
$Value_Y=array();unset($Value_Y);
$Value_W=array();unset($Value_W);
$Value_A=array();unset($Value_A);
$SumType_Y=array();unset($SumType_Y);
$SumType_W=array();unset($SumType_W);
$SumType_A=array();unset($SumType_A);
$SumCol=array();unset($SumCol);
$SumOut=array();unset($SumOut);
$DataCheck1A=array();unset($DataCheck1A);
$DataCheck1B=array();unset($DataCheck1B);
$DataCheck2A=array();unset($DataCheck2A);
$DataCheck2B=array();unset($DataCheck2B);
$DataCheck3A=array();unset($DataCheck3A);
$DataCheck3B=array();unset($DataCheck3B);
$DataCheck4A=array();unset($DataCheck4A);
$DataCheck4B=array();unset($DataCheck4B);
$DataCheck0A=array();unset($DataCheck0A);
$DataCheck0B=array();unset($DataCheck0B);
$Subscript=0;			//数组起始下标
$NowMonth=$checkMonth==""?date("Y-m-01"):$checkMonth."-01";	//起始月份：默认为当前月

$MonthCount=$MonthCount==0?6:$MonthCount;										//要显示的月份数：默认为6个月
$MonthCount=$checkMonth==""?$MonthCount:1;										//如果已指定月份，则要显示的月份数为1

/* echo $MonthCount."<br>"; */
for($Subscript=0;$Subscript<=$MonthCount;$Subscript++){
	if($Subscript==0){
		$TempPayDatetj="";
		$TempDatetj="";
		$TempMonthtj="";
		$TempSendDatetj="";
		$TempqkDatetj="";
		$TempDateTax="";
		$TempDeliveryDate="";
		}
	else{
		$StepM=$Subscript-1;
		$CheckTime=date("Y-m",strtotime("$NowMonth -$StepM month"));
		$TempPayDatetj=" AND DATE_FORMAT(M.PayDate,'%Y-%m')='$CheckTime'";
		$TempDatetj=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
		$TempMonthtj="  AND M.Month='$CheckTime'";
		$TempSendDatetj=" AND DATE_FORMAT(M.SendDate,'%Y-%m')='$CheckTime'";
		$TempqkDatetj=" AND DATE_FORMAT(M.qkDate,'%Y-%m')='$CheckTime'";
		$TempDateTax=" AND DATE_FORMAT(M.TaxDate,'%Y-%m')='$CheckTime'";
        $TempDateModelf=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
		$TempDeliveryDate=" AND DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$CheckTime'";
	}
	
	include "../../syb/desk_pandl_data.php";
}

$subTotalData = array();

for($i = 1 ; $i <= $MonthCount ; $i++) {
     $subTotalData[] = $SumCol_A[$i];
}

$returnList = array();

//起始月份：默认为当前月
$nowMonth = date("Y-m-01");	
for ($monthIndex = 0; $monthIndex < $MonthCount; $monthIndex ++) {

	$title = array(
		"text" => date("Y-m", strtotime("$nowMonth - $monthIndex month"))
	);
	
	//這是從網頁版的資料取得的月份總合
	$amount = array(
		"text" => "¥".number_format($subTotalData[$monthIndex]),
		"color" => ($subTotalData[$monthIndex] > 0) ? "#6ba733" : "#d92126",
	);
	
	$headData = array(
		"data" => array(
			"title" => $title,
			"amount" => $amount,
		)
	);
	
	//月份的資料
	$isOnTap = "0";
	$dataList = array();
	
	$monthData = array(
		"tag" => "head",
		"head" => $headData,
	);
	
	$returnList[] = $monthData;
}

$jsonArray = $returnList;
?>
		