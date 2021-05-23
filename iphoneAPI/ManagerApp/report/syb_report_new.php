<?php
include "../../basic/class.php";
$timer = new timer();
$timer->start();

 //损益表
 $today=date("Y-m-d");
 $yesterday=date("Y-m-d",strtotime("$today  -1   day"));

 $updateSign=0;
 $checksybResult= mysql_query("SELECT * FROM $DataIn.sybdata WHERE  Date='$yesterday' LIMIT 1",$link_id);
 if (mysql_num_rows($checksybResult)<=0){
	  $updateSign=1;
 }

$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE  Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
//$ChangeAction=$ChangeAction==""?6:$ChangeAction;//要显示的月份数：默认为6个月
//$MonthCount=$ChangeAction;
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

$MonthCount=$MonthCount==""?6:$MonthCount;										//要显示的月份数：默认为6个月
$MonthCount=$checkMonth==""?$MonthCount:1;										//如果已指定月份，则要显示的月份数为1
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

	if ($FromPage=="System_dayautorun"){
		include "../syb/desk_pandl_data.php";
	}
	else{
		include "../../syb/desk_pandl_data.php";
	}
}

$monthArray = array();
$MonthArray[]="项目";
$DataArray["项目"]=array();
for($i=0;$i<$MonthCount;$i++){
	$tmpMonth=date("Y-m",strtotime("$NowMonth -$i month"));
	$MonthArray[]=$tmpMonth;
	$sumMonth[$tmpMonth]=0;
	$DataArray[$tmpMonth]=array();
	$monthArray[] = $tmpMonth;
}

$MonthArray[]="已结付金额";
$MonthArray[]="未结付金额";
$MonthArray[]="小   计";
$MonthArray[]="百分比";

$ItemNameArray=array();
$SumArray_Y=array();
$SumArray_W=array();
$SumArray_A=array();
$PercentArray=array();
$tempArray=array();

//昨日的舊資料
$yesterdayData = array();
$sqlQuery = mysql_query("SELECT Amount, ItemId, Month FROM $DataIn.sybdata WHERE Date='$yesterday'", $link_id);
if($row = mysql_fetch_array($sqlQuery)) {
	do {
		$yesterdayData[] = $row;
	} while ($row = mysql_fetch_array($sqlQuery));
}

//group data
$groupData = array();

//数据输出
$rateResult = mysql_query("SELECT * FROM $DataPublic.sys8_pandlmain WHERE Estate=1 ORDER BY SortId ",$link_id);

if($rateRow = mysql_fetch_array($rateResult)) {

	$m = 0;//全部项目计数，从0开始
	$T = 0;//分类和数组下标

	//cabbage 紀錄 Value_A 的位置，Value_A 是單月的「所有項目」已結金額(各大項的子項目集合)和統計
	$valueAIndex = 0;
	do{

		$Id = $rateRow["Id"];
		//主项目名称
		$ItemName=$rateRow["ItemName"];

		//group set
		$groupSetArray = array(
			"ColTitle" => array(
				"Height" 	=> 	"25",
				"TextAlign"	=> 	"L",
				"FontSize"	=>	"13",
				"TextColor"	=>	"ColTitle",
				"BgColor"	=>	"ColBg2",
			),
			"RowTitle" => array(
				"TextAlign"	=>	"L",
				"TextColor"	=>	"Text",
				"FontSize"	=>	"13",
				"BgColor"	=>	"",
				"ColumnNo"	=>	"0"
			),
			"ColSeparatorColor"	=>	"Separator",
			"RowSeparatorColor"	=>	"",
			"OddRowTextColor"	=>	"Text",
			"OddRowBgColor"		=>	"OddRowBg",
			"EvenRowBgColor"	=>	"EvenRowBg",
			"ShowCol"			=>	"",
			"ColHeight"			=>	"43",
			"FontSize"			=>	"13",
			"BorderColor"		=>	"",
			"EvenRowTextColor"	=>	"Text",
			"TextAlign"			=>	"R",
		);

		//column title
		//大項目的header，顯示統計金額
    	$colTitleId = array( "" );
    	$colTitleData = array( $ItemName );

    	//月份
    	for ($monthIndex = 1; $monthIndex <= count($monthArray); $monthIndex++) {
	    	$colTitleId[] = $monthArray[$monthIndex - 1];
	    	$colTitleData[] = getFormatNumber($SumType_A[$monthIndex][$T]);
    	}

    	//已结付金额、未结付金额、小计
    	$colTitleId[] = "Payed";
    	$colTitleId[] = "NoPay";
    	$colTitleId[] = "Total";

    	$colTitleData[] = getFormatNumber($SumType_Y[0][$T]);
    	$colTitleData[] = getFormatNumber($SumType_W[0][$T]);
    	$colTitleData[] = getFormatNumber($SumType_A[0][$T]);

    	//相对于总支出的百分比
    	if ($T > 0) {
	    	$TempRateInAll=sprintf("%.2f", $SumType_A[0][$T] / $SumOut_A[0] * 100);
			if($TempRateInAll < 0.01) {
				$RateInAll="< 0.01%";
			}
			else {
				$RateInAll=$TempRateInAll."%";
			}
		}
		else {
			$RateInAll="100%|PosText";
		}
    	$colTitleId[] = "Per";
	    $colTitleData[] = $RateInAll;

		$groupColTitle = array(
			"Id" => $colTitleId,
			"Data" => $colTitleData
		);

    	//column data
    	$checkSubSql = mysql_query("SELECT Id, Mid, ItemName, Parameters, AjaxView, AjaxNo, Estate
    								FROM  $DataPublic.sys8_pandlsheet 
    								WHERE Mid = '$Id' 
    								AND Estate = 1 
    								ORDER BY SortId", $link_id);
		if($checkSubRow = mysql_fetch_array($checkSubSql)) {
			//子項目
			//子項目的月份明細
			$groupColData = array();

			//第一個column，項目的名稱、Id
			$subItemsList = array();
			$monthAmountData = array();

			//這個大項下有幾個子項
			$subItemsCount = 0;

			$colDataItem = array(
				"Data" => array(),
				"Id" => array(),
				"CanExpand" => "0"
			);

			unset($subItemList);
			do {
				$subItemList[] = $checkSubRow;

				if ($Value_A[0][$subItemsCount + $valueAIndex] != 0) {

					$ajaxNo = $checkSubRow["AjaxNo"];
					$parameters = $checkSubRow["Parameters"];
					$subItemName = $checkSubRow["ItemName"];	//子项目名称

					$subItemId = $ajaxNo;
					if (strlen($parameters) > 0) {
						$subItemId .= ("_".$parameters);
					}

					$idData = array(
						"Id" => $subItemId,
						"CanExpand" => $checkSubRow["AjaxView"]
					);

					array_push($colDataItem["Data"], $subItemName);
					array_push($colDataItem["Id"], $idData);

				}

				$subItemsCount ++;

			} while ($checkSubRow=mysql_fetch_array($checkSubSql));

			$groupColData[] = $colDataItem;

			//月份資料
			for ($monthIndex = 1; $monthIndex < count($Value_A); $monthIndex ++) {

				//從Value_A裡取出項目的資料
				$amountData = array_slice($Value_A[$monthIndex], $valueAIndex, $subItemsCount);

				$tmpAmount = array();

				for ($subItemIndex = 0; $subItemIndex < count($amountData); $subItemIndex ++) {
					//統計amount
					$detailAmount = $amountData[$subItemIndex];
					$amountString = number_format($detailAmount)."|";

					$subItemData = $subItemList[$subItemIndex];

					if ($Value_A[0][$valueAIndex + $subItemIndex] != 0) {

						//item id >>
						$subItemId = $subItemData["Id"];

						//item name
						$subItemName = $subItemData["ItemName"];

						$upNewSign = checkSYBfromList(	$yesterdayData,
														$subItemId,
														$monthArray[$monthIndex - 1],
														round($detailAmount));
/*
						$upNewSign = checkSYB(	$DataIn,
												$link_id,
												$subItemId,
												$monthArray[$monthIndex - 1],
												round($detailAmount),
												$yesterday);
*/
/* 						$upNewSign = 0; */
/* 						echo $monthArray[$monthIndex - 1]."<br>"; */
						if ($upNewSign == 1) {
		                	$amountString .= "VarText";
						}
		                if ($updateSign == 1) {
			                updateSYB(	$DataIn,
			                			$link_id,
			                			$subItemId,
			                			$subItemName,
			                			$monthArray[$monthIndex - 1],
			                			$detailAmount,
			                			$yesterday);
		                }

		                $tmpAmount[] = $amountString;
					}
				}

				$colDataItem = array(
					"Data" => $tmpAmount,
					"CanExpand" => "1"
				);

				$groupColData[] = $colDataItem;
			}

			//已結付金額、未結付金額、小計
			$sumData = array(
				array_slice($Value_Y[0], $valueAIndex, $subItemsCount),
				array_slice($Value_W[0], $valueAIndex, $subItemsCount),
				array_slice($Value_A[0], $valueAIndex, $subItemsCount)
			);

			foreach ($sumData as $amountData) {

				$tmpAmount = array();
				for ($subItemIndex = 0; $subItemIndex < count($amountData); $subItemIndex ++) {

					$detailAmount = $amountData[$subItemIndex];

					if ($Value_A[0][$valueAIndex + $subItemIndex] != 0) {
						$tmpAmount[] = number_format($detailAmount)."|";
					}
				}

				$colDataItem = array(
					"Data" => $tmpAmount,
					"CanExpand" => "1"
				);
				$groupColData[] = $colDataItem;

			}

			//相对于总支出的百分比
			$PercentArray = array();

			for ($itemIndex = 0; $itemIndex < $subItemsCount; $itemIndex ++) {

				if ($Value_A[0][$valueAIndex + $itemIndex] != 0) {
					$TotalOutValue = ($T > 0) ? $SumOut_A[0] : $SumType_A[0][$T];

					$TempRateInAll = sprintf("%.2f", $Value_A[0][$itemIndex + $valueAIndex] / $TotalOutValue * 100);
					if ($TempRateInAll < 0.01) {
						$RateInAll = "< 0.01%";
					}
					else {
						$RateInAll = $TempRateInAll."%";
					}

					$PercentArray[]= ($T > 0) ? $RateInAll."|" : $RateInAll."|PosText";
				}
			}
			$percentDataItem = array(
				"Data" => $PercentArray,
				"CanExpand" => "0"
			);
			$groupColData[] = $percentDataItem;

			$valueAIndex += $subItemsCount;

			$groupData[] = array(
				"Id" => $rateRow["Id"],
				"SET" => $groupSetArray,
				"ColTitle" => $groupColTitle,
				"ColData" => $groupColData
			);
		}

		$T++;//大分类累加
		}while($rateRow = mysql_fetch_array($rateResult));
	}


//2008-07-01后结付2008-07-01前的金额，因为桌面现金流水帐有计算，所以损益需要扣除这部分金额
$PreRow = mysql_fetch_array(mysql_query("
SELECT SUM(Amount) AS Amount FROM(
SELECT SUM(S.Amount) AS Amount FROM $DataIn.cwxzmain M,$DataIn.cwxzsheet S WHERE 1 AND M.PayDate>='2008-07-01' AND S.Mid=M.Id AND S.Month<'2008-07'
UNION ALL
SELECT SUM(S.Amount) AS Amount FROM $DataIn.hdjbmain M,$DataIn.hdjbsheet S WHERE 1 AND M.PayDate>='2008-07-01' AND S.Mid=M.Id AND S.Month<'2008-07'
UNION ALL
SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.cwdyfmain M,$DataIn.cwdyfsheet S,$DataPublic.currencydata C WHERE 1 AND M.PayDate>='2008-07-01' AND S.Date<'2008-07-01' AND S.Mid=M.Id AND C.Id=S.Currency
UNION ALL
SELECT SUM(S.Amount*C.Rate) AS Amount FROM $DataIn.hzqksheet S LEFT JOIN $DataIn.hzqkmain M ON S.Mid=M.Id LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency WHERE M.PayDate>='2008-07-01' AND S.Date<'2008-07-01'
) A
",$link_id));
$PreAmount=$PreRow["Amount"];

//如果是研砼，要扣除多退给客人的款，直至补回数据时，删除此数据
$PreAmount+=3000*$USD_Rate;//加上多付的客户回扣，当资料补回时需更正此处


//column header
$colTitleId = array("");
$colTitleData = array("项目");
foreach ($monthArray as $monthString) {
	$colTitleId[] = $monthString;
	$colTitleData[] = $monthString;
}
array_push($colTitleId, "Payed", "NoPay", "Total", "Per");
array_push($colTitleData, "已结付金额", "未结付金额", "小 计", "百分比");

$colHeaderData = array();
$colHeaderData[] = array(
	"Data" => array(
		"损益表统计|"
	),
	"Id" => array(
		array(
			"Id" => "total",
			"CanExpand" => "1",
		),
	),
	"CanExpand" => "0"
);

for ($i = 1; $i <= count($monthArray); $i++) {

	$sum = $SumCol_A[$i];

	$colHeaderData[] = array(
		"Data" => array(
			($sum > 0) ? getFormatNumber($sum)."PosText" : getFormatNumber($sum)."NegText"
		),
		"CanExpand" => "1"
	);
}

//已結統計
$sumTotalY = $SumCol_Y[0] - $PreAmount;
$colHeaderData[] = array(
	"Data" => array(
		($sumTotalY > 0) ? getFormatNumber($sumTotalY)."PosText" : getFormatNumber($sumTotalY)."NegText"
	),
	"CanExpand" => "1"
);

//未結統計
$sumTotalW = $SumCol_W[0];
$colHeaderData[] = array(
	"Data" => array(
		($sumTotalW > 0) ? getFormatNumber($sumTotalW)."PosText" : getFormatNumber($sumTotalW)."NegText"
	),
	"CanExpand" => "1"
);

//全部統計
$sumTotalA = $SumCol_A[0]-$PreAmount;
$colHeaderData[] = array(
	"Data" => array(
		($sumTotalA > 0) ? getFormatNumber($sumTotalA)."PosText" : getFormatNumber($sumTotalA)."NegText"
	),
	"CanExpand" => "1"
);

//最後空白
$colHeaderData[] = array(
	"Data" => array(
		"|"
	),
	"CanExpand" => "0"
);


$setArray = array(
	"LeftColWidth"	=> "80",
	"ColWidth"		=> "80",
	"ColorList"		=> array(
		"ColBg1"	=> "#e9ecf4",
		"ColBg2"	=> "#358FC1",
		"ColTitle"	=> "#e9ecf4",
		"PosText"	=> "#6ba733",
		"Text"		=> "#3b2e4e",
		"NegText"	=> "#d92126",
		"VarText"	=> "#d92126",
		"EvenRowBg"	=> "#f7f6f6",
		"OddRowBg"	=> "#ffffff",
		"Separator"	=> "#c7d0e4"
	)
);

$colHeader = array(
	"Id" => "subtotal",
	"SET" => array(
		"ColTitle" => array(
			"Height"	=> "31",
			"TextAlign"	=> "M",
			"FontSize"	=> "13",
			"TextColor"	=> "Text",
			"BgColor"	=> "ColBg1"
		),
		"RowTitle" => array(
			"TextAlign"	=> "L",
			"TextColor"	=> "Text",
			"FontSize"	=> "13",
			"BgColor"	=> "",
			"ColumnNo"	=> "0"
		),
		"ColSeparatorColor"	=> "Separator",
		"RowSeparatorColor"	=> "",
		"OddRowTextColor"	=> "PosText",
		"OddRowBgColor"		=> "OddRowBg",
		"EvenRowBgColor"	=> "EvenRowBg",
		"ShowCol"			=> "",
		"ColHeight"			=> "43",
		"FontSize"			=> "13",
		"BorderColor"		=> "",
		"EvenRowTextColor"	=> "PosText",
		"TextAlign"			=> "R"
	),
	"ColTitle" => array(
		"Id"	=> $colTitleId,
		"Data"	=> $colTitleData
	),
	"ColData"	=> $colHeaderData
);

$jsonArray = array(
	"NavTitle"	=> "损益表",
	"SET"		=> $setArray,
	"ColHeader"	=> $colHeader,
	"GroupData"	=> $groupData
);

$timer->stop();
/* echo $timer->spent()."<br>"; */
/* $jsonArray = $Value_A; */

function getFormatNumber($amount) {

	$formatAmount = "";
	if ($amount > 0) {
		$formatAmount = number_format($amount);
	}
	else {
		$formatAmount = "(".number_format(abs($amount)).")";
	}

	return $formatAmount."|";
}

function updateSYB($DataIn,$link_id,$ItemId,$ItemName,$Month,$Amount,$Date)
{
	$IN_main="REPLACE INTO $DataIn.sybdata(Id,ItemId,ItemName,Month,Amount,Date)VALUES(NULL,'$ItemId','$ItemName','$Month','$Amount','$Date')";
	$In_Result=@mysql_query($IN_main,$link_id);
}

function checkSYB($DataIn,$link_id,$ItemId,$Month,$Amount,$Date)
{
     $NewSign=0;
	 $AmountResult=mysql_query("SELECT Amount FROM  $DataIn.sybdata WHERE ItemId='$ItemId' AND Month='$Month' AND Date='$Date'",$link_id);

	 if($AmountRow = mysql_fetch_array($AmountResult)){
			 $oldAmount=$AmountRow["Amount"];
			 if ($oldAmount!=$Amount){
				   $NewSign=1;
			 }
	 }
	 return $NewSign;
}

function checkSYBfromList($yesterdayData, $itemId, $month, $amount)
{
	$newSign = false;

	foreach ($yesterdayData as $data) {
		if (($data["Month"] == $month) && ($data["ItemId"] == $itemId)) {
			if ($data["Amount"] != $amount) {
/* 				echo $data["Amount"]." >> ".$amount."<br>"; */
/* 				var_dump($data, "<br>", $amount."<br>" ); */
				$newSign = true;
/* 				echo $data['Amount']."<br>"; */
			}
			break;
		}
	}

	return $newSign;
}

?>