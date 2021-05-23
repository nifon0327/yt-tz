<?php 
/*
//EWEN 2013-10-24 部门Id<6的方计入行政统计
C支出(薪资)
生产员工薪资	/假日加班费/社保费用
仓库员工薪资	/假日加班费/社保费用 	 	 	 	 	 	 	 
品检员工薪资	/假日加班费/社保费用
开发员工薪资	/假日加班费/社保费用
采购员工薪资	/假日加班费/社保费用
MTS员工薪资	/假日加班费/社保费用
IT员工薪资		/假日加班费/社保费用
行政员工薪资	/假日加班费/社保费用
其他薪资奖金

*/
$Sum_Y=$Sum_W=$Sum_A=0;
//$Value_Y[$Subscript][]				$Value_W[$Subscript][]				$Value_A[$Subscript][]
//$SumType_Y[$Subscript][] 	 		$SumType_W[$Subscript][]		 	$SumType_A[$Subscript][]
//按实际请款月份计算

$scBidArray=array(6,7,8,14,15);
if($Subscript>0){
	//人工计算：部门为品检、仓房、车间的员工工资总额
	$checkRG=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount+M.Jz+M.Sb+M.RandP+M.Gjj+M.Ct+M.Otherkk) AS rgAmount FROM $DataIn.cwxzsheet M WHERE M.BranchId IN (6,7,8,14,15)  $TempMonthtj",$link_id));
	$DataCheck2A[$Subscript]=sprintf("%.0f",$checkRG["rgAmount"]);
	}
//已付薪资
$checkBranchSql=mysql_query("SELECT Id FROM $DataPublic.branchdata  ORDER BY SortId ",$link_id);
if($checkBranchRow=mysql_fetch_array($checkBranchSql)){
	$b=1;
	do{
		$BId=$checkBranchRow["Id"];
		//薪资  ewen 2014-06-11 加入支货付货币
		$checkXZ_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,SUM((M.Amount+M.Jz+M.Sb+M.RandP+M.Otherkk)*C.Rate) AS rgAmount FROM  $DataIn.cwxzsheet M 
																							   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
																							   WHERE M.BranchId='$BId' AND  M.Estate='0' $TempMonthtj",$link_id));
		$checkXZ_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount,SUM((M.Amount+M.Jz+M.Sb+M.RandP+M.Otherkk)*C.Rate) AS rgAmount FROM $DataIn.cwxzsheet M 
																							   LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency
																							   WHERE M.BranchId='$BId' AND M.Estate='3' $TempMonthtj",$link_id));
		//假日加班费 过滤至2013-05之前，之后就合并至薪资中
		$checkJBF_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.hdjbsheet M WHERE M.BranchId='$BId'  AND M.Estate='0' $TempMonthtj",$link_id));
		$checkJBF_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.hdjbsheet M WHERE M.BranchId='$BId' AND M.Estate='3' $TempMonthtj",$link_id));

		$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkXZ_Y["Amount"])+sprintf("%.0f",$checkJBF_Y["Amount"]); 		
		$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkXZ_W["Amount"])+sprintf("%.0f",$checkJBF_W["Amount"]);		
		$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkXZ_Y["Amount"]+$checkXZ_W["Amount"])+sprintf("%.0f",$checkJBF_Y["Amount"])+sprintf("%.0f",$checkJBF_W["Amount"]);		
		//if($Subscript>0 && $BId<6){//数据写入当月行政费用$scBidArray
		if($Subscript>0 && !in_array($BId, $scBidArray)){
			$DataCheck1A[$Subscript]+=sprintf("%.0f",$checkXZ_Y["Amount"]+$checkXZ_W["Amount"])+sprintf("%.0f",$checkJBF_Y["Amount"])+sprintf("%.0f",$checkJBF_W["Amount"]);
		}
		
		$Sum_Y+=$Value_Y[$Subscript][]=0;	
		$Sum_W+=$Value_W[$Subscript][]=0;		
		$Sum_A+=$Value_A[$Subscript][]=0;		
		//if($Subscript>0 && $BId<6){//数据写入当月行政费用
		if($Subscript>0 && !in_array($BId, $scBidArray)){
			$DataCheck1A[$Subscript]+=0;
			}


		//社保费用
		$checkSBF_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE M.BranchId='$BId' AND M.Month>='2008-07' AND M.Estate='0' AND M.TypeId=1 $TempMonthtj",$link_id));
		$checkSBF_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE M.BranchId='$BId' AND M.Month>='2008-07' AND M.Estate='3' AND M.TypeId=1 $TempMonthtj",$link_id));
		
		
		$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkSBF_Y["Amount"]);	
		$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkSBF_W["Amount"]);		
		$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkSBF_Y["Amount"])+sprintf("%.0f",$checkSBF_W["Amount"]);
		if($Subscript>0 && !in_array($BId, $scBidArray)){//数据写入当月行政费用
			$DataCheck1A[$Subscript]+=sprintf("%.0f",$checkSBF_Y["Amount"])+sprintf("%.0f",$checkSBF_W["Amount"]);
			}
		$b++;
		}while($checkBranchRow=mysql_fetch_array($checkBranchSql));
	}

		//住房公积金
		$checkGJJ_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='0' AND M.TypeId=2 $TempMonthtj",$link_id));
		$checkGJJ_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='3' AND M.TypeId=2 $TempMonthtj",$link_id));
		$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkGJJ_Y["Amount"]+$HZ682_Y);	
		$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkGJJ_W["Amount"]+$HZ682_W);		
		$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkGJJ_Y["Amount"]+$HZ682_Y)+sprintf("%.0f",$checkGJJ_W["Amount"]+$HZ682_W);
		if($Subscript>0 && !in_array($BId, $scBidArray)){//数据写入当月行政费用
			$DataCheck1A[$Subscript]+=sprintf("%.0f",$checkGJJ_Y["Amount"])+sprintf("%.0f",$checkGJJ_W["Amount"])+$HZ682_A;
			}
			
		

//其他薪资奖金=临时工薪资+621+622+635

$Sum_Y+=$Value_Y[$Subscript][]=$HZ621_Y+$HZ622_Y+$HZ635_Y;
$Sum_W+=$Value_W[$Subscript][]=$HZ621_W+$HZ622_W+$HZ635_W;
$Sum_A+=$Value_A[$Subscript][]=$HZ621_A+$HZ622_A+$HZ635_A;
/*
$checkLSGXZ_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cwxztempsheet M WHERE M.Month>='2008-07' AND M.Estate='0' $TempMonthtj",$link_id));
$checkLSGXZ_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cwxztempsheet M WHERE M.Month>='2008-07' AND M.Estate='3' $TempMonthtj",$link_id));

$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkLSGXZ_Y["Amount"])+$HZ621_Y+$HZ622_Y+$HZ635_Y;
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkLSGXZ_W["Amount"])+$HZ621_W+$HZ622_W+$HZ635_W;
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkLSGXZ_Y["Amount"])+sprintf("%.0f",$checkLSGXZ_W["Amount"])+$HZ621_A+$HZ622_A+$HZ635_A;


//辞退赔偿金

$StaffOut_1_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.staff_outsubsidysheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency  WHERE 1  AND M.Estate='0' AND M.TypeId=2  $TempDatetj",$link_id));
$StaffOut_1_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.staff_outsubsidysheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency  WHERE 1  AND M.Estate='3' AND M.TypeId=2 $TempDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$StaffOut_1_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$StaffOut_1_W["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$StaffOut_1_Y["Amount"])+sprintf("%.0f",$StaffOut_1_W["Amount"]);


//意外险
$checkYWX_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='0' AND M.TypeId=3  $TempMonthtj",$link_id));
$checkYWX_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='3' AND M.TypeId=3  $TempMonthtj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkYWX_Y["Amount"]);	
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkYWX_W["Amount"]);		
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkYWX_Y["Amount"])+sprintf("%.0f",$checkYWX_W["Amount"]);
if($Subscript>0 && !in_array($BId, $scBidArray)){//数据写入当月行政费用
   	$DataCheck1A[$Subscript]+=sprintf("%.0f",$checkYWX_Y["Amount"])+sprintf("%.0f",$checkYWX_W["Amount"]);
}
*/
	
/*	
	//商业险
$checkSYX_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='0' AND M.TypeId=4  $TempMonthtj",$link_id));
$checkSYX_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='3' AND M.TypeId=4  $TempMonthtj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkSYX_Y["Amount"]);	
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkSYX_W["Amount"]);		
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkSYX_Y["Amount"])+sprintf("%.0f",$checkSYX_W["Amount"]);
if($Subscript>0 && !in_array($BId, $scBidArray)){//数据写入当月行政费用
   	$DataCheck1A[$Subscript]+=sprintf("%.0f",$checkSYX_Y["Amount"])+sprintf("%.0f",$checkSYX_W["Amount"]);
}
*/

//其它奖金
$checkbonus_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cw20_bonussheet M WHERE  M.Estate='0' $TempDatetj",$link_id));
$checkbonus_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cw20_bonussheet M WHERE M.Estate='3' $TempDatetj",$link_id));

$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkbonus_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkbonus_W["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkbonus_Y["Amount"])+sprintf("%.0f",$checkbonus_W["Amount"]);

if($Subscript>0 && $b>1){//数据写入当月行政费用:临时工工资、621奖金和其他工资，622员工社保、635临时工工资 2013-10-24 ewen 不计入损益表的行政统计
			//$DataCheck1A[$Subscript]+=sprintf("%.0f",$checkLSGXZ_Y["Amount"])+sprintf("%.0f",$checkLSGXZ_W["Amount"])+$HZ621_A+$HZ622_A+$HZ635_A;
			}
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