<?
//电信
//代码共享-EWEN 2013-11-06 分现金和非现金
//2 支出(货款)
$Sum_Y=$Sum_W=$Sum_A=0;

//现金项目
$FKoutY_1		=$FKoutW_1		=0;	//1OK 货款：已付、未付、全部
$FKDJYD_1	=$FKDJWD_1		=0;	//2 抵货款的订金：已抵、未抵、全部(已结付总额)
$FKDJY_1		=$FKDJW_1		=0;	//3 订金：已付、未付、全部
$HKoutY_1	=$HKoutW_1		=0;	//4OK 佣金：已付、未付、全部

//非现金：30天月结+45天月结+60天月结.....
$FKoutY_0		=$FKoutW_0		=0;	//1OK 尾款：已付、未付、全部
$FKDJYD_0	=$FKDJWD_0		=0;	//2 抵货款的订金：已抵、未抵、全部(已结付总额)
$FKDJY_0		=$FKDJW_0		=0;	//3 订金：已付、未付、全部
$HKoutY_0	=$HKoutW_0		=0;	//4OK 佣金：已付、未付、全部

//1 供应商货款处理（包括订金在内，所以还需扣除相应的订金）
//未结付供应商货款：$FKoutW_1、$FKoutW_0
$gysWFSql=mysql_query("SELECT SUM((M.AddQty+M.FactualQty)*M.Price*C.Rate) AS Amount,concat('FKoutW_',P.GysPayMode) AS Name 
FROM $DataIn.cw1_fkoutsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE 1 AND M.Estate=3  $TempMonthtj GROUP BY P.GysPayMode",$link_id);					
if($gysWFRow=mysql_fetch_array($gysWFSql)){
	do{
		$Amount=sprintf("%.0f",$gysWFRow["Amount"]);
		$Name=$gysWFRow["Name"]=="FKoutW_1"?"FKoutW_1":"FKoutW_0";
		$TempFKout=strval($Name); 
		$$TempFKout+=$Amount;
		}while  ($gysWFRow=mysql_fetch_array($gysWFSql));
	}
//已付供应商货款：$FKoutY_1、$FKoutY_0
$gysYFSql=mysql_query("SELECT SUM((M.AddQty+M.FactualQty)*M.Price*C.Rate) AS Amount,concat('FKoutY_',P.GysPayMode) AS Name 
FROM $DataIn.cw1_fkoutsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE 1 AND M.Estate=0  $TempMonthtj GROUP BY P.GysPayMode",$link_id);
if($gysYFRow=mysql_fetch_array($gysYFSql)){
	do{
		$Amount=sprintf("%.0f",$gysYFRow["Amount"]);
		$Name=$gysYFRow["Name"]=="FKoutY_1"?"FKoutY_1":"FKoutY_0";;
		$TempFKout=strval($Name); 
		$$TempFKout+=$Amount;
		}while  ($gysYFRow=mysql_fetch_array($gysYFSql));
	}//供应商货款处理完毕
$FKoutA_1=$FKoutY_1+$FKoutW_1;
$FKoutA_0=$FKoutY_0+$FKoutW_0;

//4 未结付佣金：$HKoutW_1、$HKoutW_0
$gysWFSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('HKoutW_',P.GysPayMode) AS Name 
FROM $DataIn.cw1_tkoutsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE 1 AND M.Estate=3  $TempMonthtj GROUP BY P.GysPayMode",$link_id);
if($gysWFRow=mysql_fetch_array($gysWFSql)){
	do{
		$Name=$gysWFRow["Name"]=="HKoutW_1"?"HKoutW_1":"HKoutW_0";	
		$TempFKout=strval($Name);
		$$TempFKout+=sprintf("%.0f",$gysWFRow["Amount"]);
		}while  ($gysWFRow=mysql_fetch_array($gysWFSql));
	}
//已结付佣金：$HKoutY_1、$HKoutY_0
$gysYFSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('HKoutY_',P.GysPayMode) AS Name 
FROM $DataIn.cw1_tkoutsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE 1 AND M.Estate=0  $TempMonthtj GROUP BY P.GysPayMode",$link_id);
if($gysYFRow=mysql_fetch_array($gysYFSql)){
	do{
		$Name=$gysYFRow["Name"]=="HKoutY_1"?"HKoutY_1":"HKoutY_0";
		$TempFKout=strval($Name); 
		$$TempFKout+=sprintf("%.0f",$gysYFRow["Amount"]);
		}while  ($gysYFRow=mysql_fetch_array($gysYFSql));
	}
$HKoutA_1=$HKoutY_1+$HKoutW_1;
$HKoutA_0=$HKoutY_0+$HKoutW_0;

//订金处理
//未结付订金：$FKDJW_1、$FKDJW_0
$gysDj_WSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('FKDJW_',P.GysPayMode) AS Name 
FROM $DataIn.cw2_fkdjsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE 1 AND M.Mid=0 AND M.Did=0 AND M.Estate=3  $TempDatetj GROUP BY P.GysPayMode",$link_id);
if($gysDj_WRow=mysql_fetch_array($gysDj_WSql)){
	do{
		$Name=$gysDj_WRow["Name"]=="FKDJW_1"?"FKDJW_1":"FKDJW_0";
		$TempFKoutDj=strval($Name); 
		$$TempFKoutDj+=sprintf("%.0f",$gysDj_WRow["Amount"]);
		}while($gysDj_WRow=mysql_fetch_array($gysDj_WSql));
	}

//已付已抵订金：$FKDJYD_1、$FKDJYD_0
$gysDj_YDSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('FKDJYD_',P.GysPayMode) AS Name 
FROM $DataIn.cw2_fkdjsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
 LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
 WHERE 1 AND M.Mid>0 AND M.Did>0 AND M.Estate=0  $TempDatetj GROUP BY P.GysPayMode",$link_id);
if($gysDj_YDRow=mysql_fetch_array($gysDj_YDSql)){
	do{
		$Name=$gysDj_YDRow["Name"]=="FKDJYD_1"?"FKDJYD_1":"FKDJYD_0";
		$TempFKoutDj=strval($Name); 
		$$TempFKoutDj+=sprintf("%.0f",$gysDj_YDRow["Amount"]);
		}while($gysDj_YDRow=mysql_fetch_array($gysDj_YDSql));
	}

//已付未抵付：$FKDJWD_1、$FKDJWD_0
$gysDj_WDSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,concat('FKDJWD_',P.GysPayMode) AS Name 
FROM $DataIn.cw2_fkdjsheet M 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
WHERE 1 AND M.Mid>0 AND M.Did=0 AND M.Estate=0 $TempDatetj GROUP BY P.GysPayMode",$link_id);
if($gysDj_WDRow=mysql_fetch_array($gysDj_WDSql)){
	do{
		$Amount=sprintf("%.0f",$gysDj_WDRow["Amount"]);
		$Name=$gysDj_WDRow["Name"]=="FKDJWD_1"?"FKDJWD_1":"FKDJWD_0";
		$TempFKoutDj=strval($Name); 
		$$TempFKoutDj+=$Amount;
		}while($gysDj_WDRow=mysql_fetch_array($gysDj_WDSql));
	}

//1 现金货款
//已付现金货款－现金供应商已抵付订金Ａ
$Sum_Y+=$Value_Y[$Subscript][]=$FKoutY_1-$FKDJYD_1;										//已结付＝已结货款-已付已抵订金
$Sum_W+=$Value_W[$Subscript][]=$FKoutW_1-$FKDJW_1-$FKDJWD_1;														//未结付＝请款货款-未结付订金－已付未抵订金
$Sum_A+=$Value_A[$Subscript][]=$FKoutY_1-$FKDJYD_1-$FKDJWD_1+$FKoutW_1-$FKDJW_1;		//全部货款=已结货款-已付已抵订金－已付未抵订金+请款货款-未结付订金

//2 现金订金
$Sum_Y+=$Value_Y[$Subscript][]=$FKDJYD_1+$FKDJWD_1;					//已结付=已付已抵订金+已付未抵订金
$Sum_W+=$Value_W[$Subscript][]=$FKDJW_1;										//未结付＝现金未结付
$Sum_A+=$Value_A[$Subscript][]=$FKDJYD_1+$FKDJWD_1+$FKDJW_1;	//全部订金=已付已抵订金+已付未抵订金+现金未结付

//3 现金佣金
$Sum_Y+=$Value_Y[$Subscript][]=$HKoutY_1+$HKoutY_0;
$Sum_W+=$Value_W[$Subscript][]=$HKoutW_1+$HKoutW_0;
$Sum_A+=$Value_A[$Subscript][]=$HKoutY_1+$HKoutW_1+$HKoutY_0+$HKoutW_0;

//4 月结货款
$Sum_Y+=$Value_Y[$Subscript][]=$FKoutY_0-$FKDJYD_0;										//已结付＝已结货款-已付已抵订金
$Sum_W+=$Value_W[$Subscript][]=$FKoutW_0-$FKDJW_0-$FKDJWD_0;														//未结付＝请款货款-未结付订金－已付未抵订金
$Sum_A+=$Value_A[$Subscript][]=$FKoutY_0-$FKDJYD_0-$FKDJWD_0+$FKoutW_0-$FKDJW_0;		//全部货款=已结货款-已付已抵订金－已付未抵订金+请款货款-未结付订金

//5 月结订金
$Sum_Y+=$Value_Y[$Subscript][]=$FKDJYD_0+$FKDJWD_0;					//已结付=已付已抵订金+已付未抵订金
$Sum_W+=$Value_W[$Subscript][]=$FKDJW_0;										//未结付＝现金未结付
$Sum_A+=$Value_A[$Subscript][]=$FKDJYD_0+$FKDJWD_0+$FKDJW_0;	//全部订金=已付已抵订金+已付未抵订金+现金未结付

//7 月结佣(转到佣金一起）
/*$Sum_Y+=$Value_Y[$Subscript][]=$HKoutY_0;
$Sum_W+=$Value_W[$Subscript][]=$HKoutW_0;
$Sum_A+=$Value_A[$Subscript][]=$HKoutY_0+$HKoutW_0;*/

//8 免抵退增值税款
$gysSK_Y=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw2_gyssksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Mid>0 AND M.Estate=0 $TempDatetj",$link_id));
$gysSK_W=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount FROM $DataIn.cw2_gyssksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND M.Mid=0 AND M.Estate=3 $TempDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$gysSK_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$gysSK_W["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$gysSK_Y["Amount"])+sprintf("%.0f",$gysSK_W["Amount"]);

//9 客户扣款(credit note)*
$ClientKK_Y=mysql_fetch_array(mysql_query("
	SELECT SUM(Amount*-1) AS Amount FROM (
		SELECT IFNULL(SUM(S.Amount*C.Rate),0) AS Amount FROM $DataIn.cw6_orderinsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.chId LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 AND M.cwSign=0 AND M.Sign=-1 $TempDatetj
		UNION ALL
		SELECT IFNULL(SUM(S.Amount*C.Rate),0) AS Amount FROM $DataIn.cw6_orderinsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.chId LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE M.Estate=0 AND M.cwSign=2 AND M.Sign=-1 $TempDatetj
	)A",$link_id));
//货款部分4-2:扣款总金额：以出货日期计算
$ClientKK_A=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty*S.Price*C.Rate),0) AS Amount FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency WHERE 1 AND M.Estate=0 AND M.Sign=-1 $TempDatetj ",$link_id));
//3 客户退款
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$ClientKK_Y["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$ClientKK_A["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$ClientKK_A["Amount"])-sprintf("%.0f",$ClientKK_Y["Amount"]);


$Sum_Y+=$Value_Y[$Subscript][]=0;//sprintf("%.0f",$PT_Y["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=0;//sprintf("%.0f",$PT_A["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=0;//sprintf("%.0f",$PT_A["Amount"])-sprintf("%.0f",$PT_Y["Amount"]);

$SumType_Y[$Subscript][]=$Sum_Y;
$SumType_W[$Subscript][]=$Sum_W;
$SumType_A[$Subscript][]=$Sum_A;

$SumCol_Y[$Subscript]-=$Sum_Y; 
$SumCol_W[$Subscript]-=$Sum_W; 
$SumCol_A[$Subscript]-=$Sum_A; 

$SumOut_Y[$Subscript]+=$Sum_Y; 
$SumOut_W[$Subscript]+=$Sum_W; 
$SumOut_A[$Subscript]+=$Sum_A; 
?>