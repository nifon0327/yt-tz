<?php 
//电信
//代码共享-EWEN 2013-11-21 非BOM数据表有改变
//10 支出(非BOM采购) ：按主分类来统计
$Sum_Y=$Sum_W=$Sum_A=$nonbomDJ_W=$nonbomDJ_YD=$nonbomDJ_WD=0;
//已结付的非BOM货款：
$checkKF_YSql=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,concat('NONBOM',M.mainType,'_Y') AS Name 
										FROM $DataIn.nonbom11_qksheet M 
										LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=M.CompanyId 
										LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency
										WHERE M.Estate=0  $TempMonthtj GROUP BY M.mainType",$link_id);
if($checkKF_YRow=mysql_fetch_array($checkKF_YSql)){
	do{
		$Amount=sprintf("%.0f",$checkKF_YRow["Amount"]);
		$Name=$checkKF_YRow["Name"];
		$TempKF=strval($Name); 
		$$TempKF=$Amount;
		}while($checkKF_YRow=mysql_fetch_array($checkKF_YSql));
	}

//未结付的非BOM货款：按主分类来统计
$checkKF_WSql=mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,concat('NONBOM',M.mainType,'_W') AS Name 
											FROM $DataIn.nonbom11_qksheet M 
											LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=M.CompanyId
											LEFT JOIN $DataPublic.currencydata C ON C.Id=B.Currency 
											WHERE M.Estate=3 $TempMonthtj GROUP BY M.mainType",$link_id);
if($checkKF_WRow=mysql_fetch_array($checkKF_WSql)){
	do{
		$Amount=sprintf("%.0f",$checkKF_WRow["Amount"]);
		$Name=$checkKF_WRow["Name"];
		$TempKF=strval($Name); 
		$$TempKF=$Amount;
		}while($checkKF_WRow=mysql_fetch_array($checkKF_WSql));
	}
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM1_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM1_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM1_Y+$NONBOM1_W;		//1	总务费用
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM2_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM2_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM2_Y+$NONBOM2_W;		//2	生产辅料
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM3_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM3_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM3_Y+$NONBOM3_W;		//3	办公用品/耗材
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM4_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM4_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM4_Y+$NONBOM4_W;		//4	电工用品
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM5_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM5_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM5_Y+$NONBOM5_W;		//5	装修用品/材料
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM6_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM6_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM6_Y+$NONBOM6_W;		//6	办公设备
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM7_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM7_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM7_Y+$NONBOM7_W;		//7	生产设备
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM8_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM8_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM8_Y+$NONBOM8_W;		//8	开发费用
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM9_Y;					$Sum_W+=$Value_W[$Subscript][]=$NONBOM9_W;					$Sum_A+=$Value_A[$Subscript][]=$NONBOM9_Y+$NONBOM9_W;		//9	IT设备
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM10_Y;				$Sum_W+=$Value_W[$Subscript][]=$NONBOM10_W;				$Sum_A+=$Value_A[$Subscript][]=$NONBOM10_Y+$NONBOM10_W;	//10	测试仪器
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM11_Y;				$Sum_W+=$Value_W[$Subscript][]=$NONBOM11_W;				$Sum_A+=$Value_A[$Subscript][]=$NONBOM11_Y+$NONBOM11_W;	//11	杂项购置
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM12_Y;				$Sum_W+=$Value_W[$Subscript][]=$NONBOM12_W;				$Sum_A+=$Value_A[$Subscript][]=$NONBOM12_Y+$NONBOM12_W;	//12	IT软件
$Sum_Y+=$Value_Y[$Subscript][]=$NONBOM13_Y;				$Sum_W+=$Value_W[$Subscript][]=$NONBOM13_W;				$Sum_A+=$Value_A[$Subscript][]=$NONBOM13_Y+$NONBOM13_W;	//13	运费
/*
取消订金功能
//加入未抵付非BOM订金、非BOM预付订金
//未结付订金：以请款日期计算Estate=3
	$nonbomDJRow_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.nonbom11_djsheet M LEFT JOIN $DataPublic.nonbom3_retailermain P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE 1 AND M.Mid=0 AND M.Did=0 AND M.Estate=3 $TempDatetj ",$link_id));
	$nonbomDJ_W=$nonbomDJRow_W["Amount"];
//已结付订金，已抵付	OK
	$nonbomDJRow_YD=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.nonbom11_djsheet M LEFT JOIN $DataPublic.nonbom3_retailermain P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE 1 AND M.Mid>0 AND M.Did>0 AND M.Estate=0 $TempDatetj",$link_id));
	$nonbomDJ_YD=$nonbomDJRow_YD["Amount"];
//已结付订金，未抵付
	$nonbomDJRow_WD=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.nonbom11_djsheet M LEFT JOIN $DataPublic.nonbom3_retailermain P ON P.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE 1 AND M.Mid>0 AND M.Did=0 AND M.Estate=0 $TempDatetj",$link_id));
	$nonbomDJ_WD=$nonbomDJRow_WD["Amount"];
	*/

//14 预付定金
	$Sum_Y+=$Value_Y[$Subscript][]=0;
	$Sum_W+=$Value_W[$Subscript][]=0;
	$Sum_A+=$Value_A[$Subscript][]=0;
	
//15 开发费用：并入非BOM其他项目
	$checkKF_YRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.cwdyfsheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1  AND M.Date>='2008-07-01' AND M.Estate=0  $TempDatetj",$link_id));
	$KF_Y=$checkKF_YRow["Amount"]+$HZ681_Y;
	$checkKF_WRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.cwdyfsheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Date>='2008-07-01' AND M.Estate=3  $TempDatetj",$link_id));
	$KF_W=$checkKF_WRow["Amount"]+$HZ681_W;
	$KF_A=$KF_Y+$KF_W;
//15 总务费用初始化：并入非BOM其他项目
	/*$checkZWYRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Qty*M.Price),0) AS Amount  FROM $DataIn.zw3_purchases M LEFT JOIN $DataIn.zw3_purchaset S ON S.Id=M.TypeId WHERE M.qkDate>='2008-07-01' AND M.Estate='0'  $TempqkDatetj",$link_id));
	$ZW_Y=$checkZWYRow["Amount"];
	$checkZWWRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Qty*M.Price),0) AS Amount FROM $DataIn.zw3_purchases M  LEFT JOIN $DataIn.zw3_purchaset S ON S.Id=M.TypeId WHERE M.qkDate>='2008-07-01' AND M.Estate='3'  $TempqkDatetj",$link_id));
	$ZW_W=$checkZWWRow["Amount"];
	$ZW_A=$ZW_Y+$ZW_W;*/
//15 非BOM其他项目：原生产和开发支出
$Sum_Y+=$Value_Y[$Subscript][]   =$KF_Y	+$ZW_Y;						
$Sum_W+=$Value_W[$Subscript][]=$KF_W	+$ZW_W;							
$Sum_A+=$Value_A[$Subscript][]  =$KF_A	+$ZW_A;

$SumType_Y[$Subscript][]=$Sum_Y;
$SumType_W[$Subscript][]=$Sum_W;
$SumType_A[$Subscript][]=$Sum_A;
$SumCol_Y[$Subscript]-=$Sum_Y; 
$SumCol_W[$Subscript]-=$Sum_W; 
$SumCol_A[$Subscript]-=$Sum_A; 
$SumOut_Y[$Subscript]+=$Sum_Y; 
$SumOut_W[$Subscript]+=$Sum_W; 
$SumOut_A[$Subscript]+=$Sum_A; 
if($Subscript>0){//数据写入当月行政费用：1、总务费用；3、办公用品/耗材；4、电工用品；11	、杂项购置
			$DataCheck1A[$Subscript]+=$NONBOM1_Y+$NONBOM1_W+$NONBOM3_Y+$NONBOM3_W+$NONBOM4_Y+$NONBOM4_W+$NONBOM11_Y+$NONBOM11_W;
			}
?>