<?php 
//-EWEN 2013-11-06
$Sum_Y=$Sum_W=$Sum_A=0;
//$Value_Y[$Subscript][]				$Value_W[$Subscript][]				$Value_A[$Subscript][]
//$SumType_Y[$Subscript][] 	 		$SumType_W[$Subscript][]		 	$SumType_A[$Subscript][]
//E69-三节奖金
$checkJJ_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cw11_jjsheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='0' $TempMonthtj",$link_id));
$checkJJ_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cw11_jjsheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='3' $TempMonthtj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkJJ_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkJJ_W["Amount"]);
$SumE+=$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkJJ_Y["Amount"])+sprintf("%.0f",$checkJJ_W["Amount"]);

$Sum_Y+=$Value_Y[$Subscript][]=$HZ672_Y+$HZ651_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ672_W+$HZ651_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ672_A+$HZ651_A;							//E70-672员工购房补助+G83-651地产
//$Sum_Y+=$Value_Y[$Subscript][]=$HZ651_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ651_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ651_A;							//G83-651地产

$Sum_Y+=$Value_Y[$Subscript][]=$HZ669_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ669_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ669_A;							//E71-669干部分红
$Sum_Y+=$Value_Y[$Subscript][]=$HZ633_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ633_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ633_A;							//E72-633购车款
$Sum_Y+=$Value_Y[$Subscript][]=$HZ623_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ623_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ623_A;							//E73-623特支费
//E74-员工借支
$checkYGJZ_Y=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.cwygjz M WHERE 1 AND M.PayDate>='2008-07-01' $TempPayDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkYGJZ_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=0;
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkYGJZ_Y["Amount"]);

///***************员工福利
$Sum_Y+=$Value_Y[$Subscript][]=$HZ631_Y+$HZ695_Y+$HZ696_Y;						
$Sum_W+=$Value_W[$Subscript][]=$HZ631_W+$HZ695_W+$HZ696_W;							
$Sum_A+=$Value_A[$Subscript][]=$HZ631_A+$HZ695_A+$HZ696_A;							//E75-631员工福利

$Sum_Y+=$Value_Y[$Subscript][]=$HZ670_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ670_W;							$Sum_A+=$Value_A[$Subscript][]=$HZ670_A;							//E76-670员工保险费用
//员工助学费用
$ChildStudy_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cw19_studyfeesheet M WHERE 1 AND M.Estate='0' $TempMonthtj",$link_id));
$ChildStudy_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.cw19_studyfeesheet M WHERE 1 AND M.Estate='3' $TempMonthtj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$ChildStudy_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$ChildStudy_W["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$ChildStudy_Y["Amount"])+sprintf("%.0f",$ChildStudy_W["Amount"]);

//员工离职补助
$StaffOut_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.staff_outsubsidysheet M
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency   WHERE 1  AND M.Estate='0'  AND  M.TypeId=1  $TempDatetj",$link_id));
$StaffOut_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.staff_outsubsidysheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency  WHERE 1  AND M.Estate='3'  AND M.TypeId=1 $TempDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$StaffOut_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$StaffOut_W["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$StaffOut_Y["Amount"])+sprintf("%.0f",$StaffOut_W["Amount"]);

//辞退赔偿金

$StaffOut_1_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.staff_outsubsidysheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency  WHERE 1  AND M.Estate='0' AND M.TypeId=2  $TempDatetj",$link_id));
$StaffOut_1_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount*C.Rate),0) AS Amount FROM $DataIn.staff_outsubsidysheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency  WHERE 1  AND M.Estate='3' AND M.TypeId=2 $TempDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$StaffOut_1_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$StaffOut_1_W["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$StaffOut_1_Y["Amount"])+sprintf("%.0f",$StaffOut_1_W["Amount"]);

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