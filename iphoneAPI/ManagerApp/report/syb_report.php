<?php
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
$ChangeAction=$ChangeAction==""?6:$ChangeAction;//要显示的月份数：默认为6个月

$MonthCount=$ChangeAction;
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

 $MonthArray[]="项目";
 $DataArray["项目"]=array();
 for($i=0;$i<$MonthCount;$i++){
      $tmpMonth=date("Y-m",strtotime("$NowMonth -$i month"));
      $MonthArray[]=$tmpMonth;
      $sumMonth[$tmpMonth]=0;
      $DataArray[$tmpMonth]=array();
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

  //数据输出
  $rateResult = mysql_query("SELECT * FROM $DataPublic.sys8_pandlmain WHERE Estate=1 ORDER BY SortId ",$link_id);

if($rateRow = mysql_fetch_array($rateResult)){

	$m=0;//全部项目计数，从0开始
	$T=0;//分类和数组下标
	do{
		$flag=0;
		$ItemMid=$T+1;
		$Id=$rateRow["Id"];							//主项目ID
		$ItemName=$rateRow["ItemName"];//主项目名称
		$ColorCode=$rateRow["ColorCode"];//行底色

		$bgColor="#24ADE2";
		$ItemNameArray[]=$ItemName . "||$bgColor";
		for($i=1;$i<=$MonthCount;$i++){
		    $tmpMonth=$MonthArray[$i];
             $DataArray[$tmpMonth][]=number_format($SumType_A[$i][$T]) . "||$bgColor";
		}
		$SumArray_Y[]=number_format($SumType_Y[0][$T]) . "||$bgColor";
		$SumArray_W[]=number_format($SumType_W[0][$T]) . "||$bgColor";
        $SumArray_A[]=number_format($SumType_A[0][$T]) . "||$bgColor";

		  //相对于总支出的百分比
		  if ($T>0){
			$TempRateInAll=sprintf("%.2f",$SumType_A[0][$T]/$SumOut_A[0]*100);
			if($TempRateInAll<0.01){
				$RateInAll="< 0.01%";
				}
			else{
				$RateInAll=$TempRateInAll."%";
				}
		   } else $RateInAll="100%";
          $PercentArray[]=$T>0?$RateInAll. "||$bgColor":$RateInAll. "|#629A50|$bgColor";

		$checkSubSql=mysql_query("SELECT * FROM  $DataPublic.sys8_pandlsheet WHERE Mid='$Id' AND Estate=1 ORDER BY SortId",$link_id);
		if($checkSubRow=mysql_fetch_array($checkSubSql)){
		     $n=0;
		  do{//每一子项目数据处理
				    if($Value_A[0][$m]!=0){
					        $SubItemId=$checkSubRow["Id"];						//子项目ID
							$SubItemName=$checkSubRow["ItemName"];	//子项目名称

							$bgColor=$n%2==0?$ColorCode:"#FFFFFF";
							$ItemNameArray[]=$SubItemName  . "||$bgColor";
							for($i=1;$i<=$MonthCount;$i++){//按月份数输出月份数据
							   $tmpMonth=$MonthArray[$i];
							   $upNewSign= checkSYB($DataIn,$link_id,$SubItemId,$tmpMonth,round($Value_A[$i][$m]),$yesterday);
							   $txtColor=$upNewSign==1?"#FF0000":"";
			                    $DataArray[$tmpMonth][]=number_format($Value_A[$i][$m])  . "|$txtColor|$bgColor";
			                    if ($updateSign==1) updateSYB($DataIn,$link_id,$SubItemId,$SubItemName,$tmpMonth,$Value_A[$i][$m],$yesterday);
							 }

							 $SumArray_Y[]=number_format($Value_Y[0][$m]) . "||$bgColor";
					         $SumArray_W[]=number_format($Value_W[0][$m]) . "||$bgColor";
			                 $SumArray_A[]=number_format($Value_A[0][$m]) . "||$bgColor";

			                 //相对于总支出的百分比
			                 $TotalOutValue=$T>0?$SumOut_A[0]:$SumType_A[0][$T];

							$TempRateInAll=sprintf("%.2f",$Value_A[0][$m]/$TotalOutValue*100);
							if($TempRateInAll<0.01){
								$RateInAll="< 0.01%";
								}
							else{
								$RateInAll=$TempRateInAll."%";
								}

                               $PercentArray[]=$T>0?$RateInAll. "||$bgColor":$RateInAll. "|#629A50|$bgColor";
			                  $n++;
		                 }
                  $m++;	//全部项目计数，从0开始
				}while($checkSubRow=mysql_fetch_array($checkSubSql));
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

//总计
$bgColor="#CCCCCC";
$bgColor2="#629A50";
$ItemNameArray[]="    损益表统计||$bgColor";
for($i=1;$i<=$MonthCount;$i++){
     $tmpMonth=$MonthArray[$i];
     $textColor=$SumCol_A[$i]>0?$bgColor2:"#FF0000";
     $sumValue=$SumCol_A[$i]>0?number_format($SumCol_A[$i]):"(" . number_format(abs($SumCol_A[$i])) . ")";
     $DataArray[$tmpMonth][]="$sumValue|$textColor|$bgColor";
}

$SumY0STR=$SumCol_Y[0]-$PreAmount;
$textColor=$SumY0STR>0?$bgColor2:"#FF0000";
$sumValue=$SumY0STR>0?number_format($SumY0STR):"(" . number_format(abs($SumY0STR)) . ")";
$SumArray_Y[]=$sumValue . "|$textColor|$bgColor";

$textColor=$SumCol_W[0]>0?$bgColor2:"#FF0000";
$sumValue=$SumCol_W[0]>0?number_format($SumCol_W[0]):"(" . number_format(abs($SumCol_W[0])) . ")";
$SumArray_W[]=$sumValue. "|$textColor|$bgColor";

$SumA0STR=$SumCol_A[0]-$PreAmount;
$textColor=$SumA0STR>0?$bgColor2:"#FF0000";
$sumValue=$SumA0STR>0?number_format($SumA0STR):"(" . number_format(abs($SumA0STR)) . ")";
$SumArray_A[]=$sumValue . "|$textColor|$bgColor";
$PercentArray[]="||$bgColor";

$tempArray[]=$ItemNameArray;
for($i=1;$i<=$MonthCount;$i++){
		    $tmpMonth=$MonthArray[$i];
             $tempArray[]=$DataArray[$tmpMonth];
}

$tempArray[]=$SumArray_Y;
$tempArray[]=$SumArray_W;
$tempArray[]=$SumArray_A;
$tempArray[]=$PercentArray;

$SetArray=array("NavTitle"=>"$NavTitle","ShowCol"=>"$SelMonth",
                             "LeftColWidth"=>"120","ColHeight"=>"25","ColWidth"=>"100",
                            "Title"=>array("Height"=>"30","Color"=>"#666666","BgColor"=>"#DDDDDD","FontSize"=>"13"),
							"TextColor"=>"#000000","TextAlign"=>"R","FontSize"=>"13","BorderColor"=>"","SeparatorColor"=>""
							      );

$jsonArray=array("SET"=>$SetArray,"ColTitle"=>$MonthArray,"ColData"=>$tempArray);

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
?>