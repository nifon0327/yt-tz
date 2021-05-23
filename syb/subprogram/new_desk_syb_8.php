<?php 
//电信
//代码共享-EWEN 2012-08-19
//8支出(运费)
$Sum_Y=$Sum_W=$Sum_A=0;
//$Value_Y[$Subscript][]				$Value_W[$Subscript][]				$Value_A[$Subscript][]
//$SumType_Y[$Subscript][] 	 		$SumType_W[$Subscript][]		 	$SumType_A[$Subscript][]


$Sum_Y+=$Value_Y[$Subscript][]=$HZ632_Y;						
$Sum_W+=$Value_W[$Subscript][]=$HZ632_W;							
$Sum_A+=$Value_A[$Subscript][]=$HZ632_A;							//H85-632代付运费+中港运费入仓费代付费用
//H86-中港运费、入仓费:以出货日期计算且为自付费用 PayType=0
$checkFreightY=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											 SELECT IFNULL(SUM(W.Amount+W.depotCharge),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1'  AND W.PayType=0 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount+W.depotCharge),0) AS Amount FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE W.Estate=0 AND W.TypeId='2'  AND W.PayType=0 $TempDeliveryDate
											)A
											",$link_id));
$checkFreightW=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											 SELECT IFNULL(SUM(W.Amount+W.depotCharge),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND W.PayType=0 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount+W.depotCharge),0) AS Amount FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE W.Estate=3 AND W.TypeId='2'  AND W.PayType=0 $TempDeliveryDate
											 )A
											 ",$link_id));
									
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkFreightY["Amount"]);	
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkFreightW["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkFreightY["Amount"])+sprintf("%.0f",$checkFreightW["Amount"]);

//H87-货代杂费Forward费用：以出货日期计算
$checkForwardY=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM (
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1'  AND W.PayType=0 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2'  AND W.PayType=0 $TempDeliveryDate
											 ) A
											 ",$link_id));
$checkForwardW=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND W.PayType=0 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2'  AND W.PayType=0 $TempDeliveryDate
											 ) A
											 ",$link_id));
											 
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkForwardY["Amount"]*$HKD_Rate);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkForwardW["Amount"]*$HKD_Rate);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkForwardY["Amount"]*$HKD_Rate)+sprintf("%.0f",$checkForwardW["Amount"]*$HKD_Rate);

//H88-报关费商检费:以出货日期计算
$checkBgY=mysql_fetch_array(mysql_query("
										SELECT SUM(Amount) AS Amount FROM (
										SELECT IFNULL(SUM(W.declarationCharge+W.checkCharge+W.carryCharge+W.xyCharge+W.wfqgCharge+W.ccCharge+W.djCharge+W.stopcarCharge+W.expressCharge+W.otherCharge),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1'   $TempDatetj
										UNION ALL
										SELECT IFNULL(SUM(W.declarationCharge+W.checkCharge+W.carryCharge+W.xyCharge+W.wfqgCharge+W.ccCharge+W.djCharge+W.stopcarCharge+W.expressCharge+W.otherCharge),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2'   $TempDeliveryDate)A
										",$link_id));
									$checkBgW=mysql_fetch_array(mysql_query("
										SELECT SUM(Amount) AS Amount FROM (
										SELECT IFNULL(SUM(W.declarationCharge+W.checkCharge+W.carryCharge+W.xyCharge+W.wfqgCharge+W.ccCharge+W.djCharge+W.stopcarCharge+W.expressCharge+W.otherCharge),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1'  $TempDatetj
										UNION ALL
										SELECT IFNULL(SUM(W.declarationCharge+W.checkCharge+W.carryCharge+W.xyCharge+W.wfqgCharge+W.ccCharge+W.djCharge+W.stopcarCharge+W.expressCharge+W.otherCharge),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2'  $TempDeliveryDate)A
										",$link_id));
				
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkBgY["Amount"])+$HZ675_Y;
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkBgW["Amount"])+$HZ675_W;
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkBgY["Amount"]+$checkBgW["Amount"])+$HZ675_A;
						

$SumType_Y[$Subscript][]=$Sum_Y;
$SumType_W[$Subscript][]=$Sum_W;
$SumType_A[$Subscript][]=$Sum_A;
$SumCol_Y[$Subscript]-=$Sum_Y; 
$SumCol_W[$Subscript]-=$Sum_W; 
$SumCol_A[$Subscript]-=$Sum_A; 
$SumOut_Y[$Subscript]+=$Sum_Y; 
$SumOut_W[$Subscript]+=$Sum_W; 
$SumOut_A[$Subscript]+=$Sum_A; 
if($Subscript>0){//实际FOB
			$DataCheck4A[$Subscript]=$Sum_A;
			}
?>