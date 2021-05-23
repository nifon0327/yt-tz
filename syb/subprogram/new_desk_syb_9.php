<?php 
//已取消 2013-10-24 ewen
//9 支出(开发)
$Sum_Y=$Sum_W=$Sum_A=0;
//$Value_Y[$Subscript][]				$Value_W[$Subscript][]				$Value_A[$Subscript][]
//$SumType_Y[$Subscript][] 	 		$SumType_W[$Subscript][]		 	$SumType_A[$Subscript][]
//开发费用+机模费用626+模具费用627+样品费630 以请款日期计算：OK
$checkKF_YSql=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,concat('KF',M.TypeID,'_Y') AS Name FROM $DataIn.cwdyfsheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1  AND M.Date>='2008-07-01' AND M.Estate=0  $TempDatetj GROUP BY M.TypeID",$link_id);
if($checkKF_YRow=mysql_fetch_array($checkKF_YSql)){
	do{
		$Amount=sprintf("%.0f",$checkKF_YRow["Amount"]);
		$Name=$checkKF_YRow["Name"];
		$TempKF=strval($Name); 
		$$TempKF=$Amount;
		}while($checkKF_YRow=mysql_fetch_array($checkKF_YSql));
	}

$checkKF_WSql=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,concat('KF',M.TypeID,'_W') AS Name FROM $DataIn.cwdyfsheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1  AND M.Date>='2008-07-01' AND M.Estate=3  $TempDatetj  GROUP BY M.TypeID",$link_id);
if($checkKF_WRow=mysql_fetch_array($checkKF_WSql)){
	do{
		$Amount=sprintf("%.0f",$checkKF_WRow["Amount"]);
		$Name=$checkKF_WRow["Name"];
		$TempKF=strval($Name); 
		$$TempKF=$Amount;
		}while($checkKF_WRow=mysql_fetch_array($checkKF_WSql));
	}
$Sum_Y+=$Value_Y[$Subscript][]=$KF1_Y+$HZ681_Y;	$Sum_W+=$Value_W[$Subscript][]=$KF1_W+$HZ681_W;	$Sum_A+=$Value_A[$Subscript][]=$KF1_Y+$KF1_W+$HZ681_Y+$HZ681_W;	//1刀模+行政681刀模
$Sum_Y+=$Value_Y[$Subscript][]=$KF2_Y;					$Sum_W+=$Value_W[$Subscript][]=$KF2_W;						$Sum_A+=$Value_A[$Subscript][]=$KF2_Y+$KF2_W;										//2射出模具
$Sum_Y+=$Value_Y[$Subscript][]=$KF3_Y;					$Sum_W+=$Value_W[$Subscript][]=$KF3_W;						$Sum_A+=$Value_A[$Subscript][]=$KF3_Y+$KF3_W;										//3定型
$Sum_Y+=$Value_Y[$Subscript][]=$KF4_Y+$KF5_Y;		$Sum_W+=$Value_W[$Subscript][]=$KF4_W+$KF5_W;		$Sum_A+=$Value_A[$Subscript][]=$KF4_Y+$KF4_W+$KF5_Y+$KF5_W;				//4、5五金、高周波
$Sum_Y+=$Value_Y[$Subscript][]=$KF6_Y;					$Sum_W+=$Value_W[$Subscript][]=$KF6_W;						$Sum_A+=$Value_A[$Subscript][]=$KF6_Y+$KF6_W;										//6电压
$Sum_Y+=$Value_Y[$Subscript][]=$KF7_Y+$KF8_Y;		$Sum_W+=$Value_W[$Subscript][]=$KF7_W+$KF8_W;		$Sum_A+=$Value_A[$Subscript][]=$KF7_Y+$KF8_Y+$KF7_W+$KF8_W;				//7、8烫金模、丝、移
$Sum_Y+=$Value_Y[$Subscript][]=$KF9_Y+$KF10_Y;		$Sum_W+=$Value_W[$Subscript][]=$KF9_W+$KF10_W;		$Sum_A+=$Value_A[$Subscript][]=$KF9_Y+$KF10_Y+$KF9_W+$KF10_W;			//9、10水贴纸、烫片
$Sum_Y+=$Value_Y[$Subscript][]=$KF11_Y;					$Sum_W+=$Value_W[$Subscript][]=$KF11_W;					$Sum_A+=$Value_A[$Subscript][]=$KF11_Y+$KF11_W;									//11布料、皮料、织带
$Sum_Y+=$Value_Y[$Subscript][]=$KF12_Y;					$Sum_W+=$Value_W[$Subscript][]=$KF12_W;					$Sum_A+=$Value_A[$Subscript][]=$KF12_Y+$KF12_W;									//12手机、机模
$Sum_Y+=$Value_Y[$Subscript][]=$KF13_Y;					$Sum_W+=$Value_W[$Subscript][]=$KF13_W;					$Sum_A+=$Value_A[$Subscript][]=$KF13_Y+$KF13_W;									//13样品
$Sum_Y+=$Value_Y[$Subscript][]=$KF14_Y;					$Sum_W+=$Value_W[$Subscript][]=$KF14_W;					$Sum_A+=$Value_A[$Subscript][]=$KF14_Y+$KF14_W;									//14治具
$Sum_Y+=$Value_Y[$Subscript][]=$KF15_Y;					$Sum_W+=$Value_W[$Subscript][]=$KF15_W;					$Sum_A+=$Value_A[$Subscript][]=$KF15_Y+$KF15_W;									//15检测费＼测试费


$SumType_Y[$Subscript][]=$Sum_Y;
$SumType_W[$Subscript][]=$Sum_W;
$SumType_A[$Subscript][]=$Sum_A;
$SumCol_Y[$Subscript]-=$Sum_Y; 
$SumCol_W[$Subscript]-=$Sum_W; 
$SumCol_A[$Subscript]-=$Sum_A; 
$SumOut_Y[$Subscript]+=$Sum_Y; 
$SumOut_W[$Subscript]+=$Sum_W; 
$SumOut_A[$Subscript]+=$Sum_A; 
if($Subscript>0){//数据写入当月行政费用
			//$DataCheck1A[$Subscript]+=$Sum_A;
			}
?>