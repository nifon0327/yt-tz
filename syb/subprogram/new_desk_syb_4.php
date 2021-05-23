<?php 
//EWEN 2013-10-24 取消部分项目计入行政统计
//4 支出(行政)
$Sum_Y=$Sum_W=$Sum_A=0;
//$Value_Y[$Subscript][]				$Value_W[$Subscript][]				$Value_A[$Subscript][]
//$SumType_Y[$Subscript][] 	 		$SumType_W[$Subscript][]		 	$SumType_A[$Subscript][]
//D40-601厂房租金
$HZ601_Y+=$HZ684_Y+$HZ685_Y+$HZ699_Y;
$HZ601_W+=$HZ684_W+$HZ685_W+$HZ699_W;
$HZ601_A+=$HZ684_A+$HZ685_A+$HZ699_A;	

$Sum_Y+=$Value_Y[$Subscript][]=$HZ601_Y;						
$Sum_W+=$Value_W[$Subscript][]=$HZ601_W;							
$Sum_A+=$Value_A[$Subscript][]=$HZ601_A;		
//D41-602,603,605厂房水电管理费,宿舍水电管理费		
$HZ602_Y+=$HZ700_Y+$HZ708_Y+$HZ712_Y;
$HZ602_W+=$HZ700_W+$HZ708_W+$HZ712_W;	
$HZ602_A+=$HZ700_A+$HZ708_A+$HZ712_A;

$Sum_Y+=$Value_Y[$Subscript][]=$HZ602_Y+$HZ603_Y+$HZ605_Y;		
$Sum_W+=$Value_W[$Subscript][]=$HZ602_W+$HZ603_W+$HZ605_W;			
$Sum_A+=$Value_A[$Subscript][]=$HZ602_A+$HZ603_A+$HZ605_A;			
//D42-604宿舍租金
$Sum_Y+=$Value_Y[$Subscript][]=$HZ604_Y;						
$Sum_W+=$Value_W[$Subscript][]=$HZ604_W;							
$Sum_A+=$Value_A[$Subscript][]=$HZ604_A;						
//D44-607电话网络费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ607_Y+$HZ713_Y;						
$Sum_W+=$Value_W[$Subscript][]=$HZ607_W+$HZ713_W;							
$Sum_A+=$Value_A[$Subscript][]=$HZ607_A+$HZ713_A;						

//D45-618+国内快递费(包括月结和现金)

$checkKDF1_Y=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.ch9_expsheet M WHERE 1 AND M.Date>='2008-07-01'  AND M.Estate='0'  $TempDatetj",$link_id));
$checkKDF1_W=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.ch9_expsheet M WHERE 1 AND M.Date>='2008-07-01'  AND M.Estate='3'  $TempDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkKDF1_Y["Amount"])+$HZ618_Y;
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkKDF1_W["Amount"])+$HZ618_W;
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkKDF1_Y["Amount"])+sprintf("%.0f",$checkKDF1_W["Amount"])+$HZ618_A;

//D46-国外快递费:寄样费
$checkKDF2_Y=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.ch10_samplemail M WHERE 1 AND M.SendDate>='2008-07-01' AND M.Estate='0'  $TempSendDatetj",$link_id));
$checkKDF2_W=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.ch10_samplemail M WHERE 1 AND M.SendDate>='2008-07-01' AND M.Estate='3'  $TempSendDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkKDF2_Y["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkKDF2_W["Amount"]);
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkKDF2_Y["Amount"])+sprintf("%.0f",$checkKDF2_W["Amount"]);

	//D47-613差旅费
$HZ613_Y+=$HZ689_Y+$HZ688_Y;
$HZ613_W+=$HZ689_W+$HZ688_W;
$HZ613_A+=$HZ689_A+$HZ688_A; 
$Sum_Y+=$Value_Y[$Subscript][]=$HZ613_Y;		$Sum_W+=$Value_W[$Subscript][]=$HZ613_W;	$Sum_A+=$Value_A[$Subscript][]=$HZ613_A;	
	//D48-615交际费					
$Sum_Y+=$Value_Y[$Subscript][]=$HZ615_Y;		$Sum_W+=$Value_W[$Subscript][]=$HZ615_W;	$Sum_A+=$Value_A[$Subscript][]=$HZ615_A;						

//D49-609车辆费用
$carfee_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.Amount),0) AS Amount FROM $DataIn.carfee M WHERE 1 AND M.Estate='0'  $TempDatetj",$link_id));
$carfee_W=mysql_fetch_array(mysql_query("SELECT  IFNULL(SUM(M.Amount),0)  AS Amount FROM $DataIn.carfee M WHERE 1 AND M.Estate='3'  $TempDatetj",$link_id));
if ($carfee_Y["Amount"]>0 || $carfee_Y["Amount"]>0){
	$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$carfee_Y["Amount"]);						
	$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$carfee_W["Amount"]);							
	$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$carfee_Y["Amount"])+sprintf("%.0f",$carfee_W["Amount"]);						
}

//D54-617银行手续费+汇款手续费
$Sql51=mysql_fetch_array(mysql_query("SELECT SUM(M.Handingfee*C.Rate) AS Amount FROM $DataIn.cw6_orderinmain M, $DataIn.trade_object K, $DataPublic.currencydata C WHERE K.CompanyId=M.CompanyId AND K.Currency=C.Id AND M.PayDate>='2008-07-01' $TempPayDatetj",$link_id));
$Sum_Y+=$Value_Y[$Subscript][]=$HZ617_Y+sprintf("%.0f",$Sql51["Amount"]);
$Sum_W+=$Value_W[$Subscript][]=$HZ617_W;
$Sum_A+=$Value_A[$Subscript][]=$HZ617_A+sprintf("%.0f",$Sql51["Amount"]);

//D55-650认证、参展费
$Sum_Y+=$Value_Y[$Subscript][]=$HZ650_Y;		$Sum_W+=$Value_W[$Subscript][]=$HZ650_W;		$Sum_A+=$Value_A[$Subscript][]=$HZ650_A;								//D56-664员工证照费
$Sum_Y+=$Value_Y[$Subscript][]=$HZ664_Y;		$Sum_W+=$Value_W[$Subscript][]=$HZ664_W;		$Sum_A+=$Value_A[$Subscript][]=$HZ664_A;							
//D57-665体检费
$checkTJF_Y=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.cw17_tjsheet M WHERE 1 AND M.Estate='0'  $TempMonthtj",$link_id));
$checkTJF_W=mysql_fetch_array(mysql_query("SELECT SUM(M.Amount) AS Amount FROM $DataIn.cw17_tjsheet M WHERE 1 AND M.Estate='3'  $TempMonthtj",$link_id));

$Sum_Y+=$Value_Y[$Subscript][]=$HZ665_Y+sprintf("%.0f",$checkTJF_Y["Amount"]);						
$Sum_W+=$Value_W[$Subscript][]=$HZ665_W+sprintf("%.0f",$checkTJF_W["Amount"]);						
$Sum_A+=$Value_A[$Subscript][]=$HZ665_A+sprintf("%.0f",$checkTJF_Y["Amount"])+sprintf("%.0f",$checkTJF_W["Amount"]);	
/*
//中港代付运费
$freightY=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=1 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=1 $TempDeliveryDate
											)A
											",$link_id));
$freightW=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=1 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.mcWG*W.Price+W.depotCharge*$HKD_Rate),0) AS Amount FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch4_freight_declaration W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=1 $TempDeliveryDate
											 )A
											 ",$link_id));
$HZ668_Y+=$tmpFreight_Y=sprintf("%.0f",$freightY["Amount"]);	
$HZ668_W+=$tmpFreight_W=sprintf("%.0f",$freightW["Amount"]);
$HZ668_A+=$tmpFreight_A=sprintf("%.0f",$freightY["Amount"])+sprintf("%.0f",$freightW["Amount"]);

//H87-货代杂费Forward费用：以出货日期计算
$forwardY=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM (
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=0 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=1 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE 1 AND W.Estate=0 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=1 $TempDeliveryDate
											 ) A
											 ",$link_id));
$forwardW=mysql_fetch_array(mysql_query("
											 SELECT SUM(Amount) AS Amount FROM(
											SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE M.Estate=0 AND W.Estate=3 AND W.TypeId='1' AND M.Date>='2008-07-01' AND W.PayType=1 $TempDatetj
											 UNION ALL
											 SELECT IFNULL(SUM(W.Amount),0) AS Amount  FROM $DataIn.ch1_deliverymain M LEFT JOIN $DataIn.ch3_forward W ON W.chId=M.Id WHERE 1 AND W.Estate=3 AND W.TypeId='2' AND M.DeliveryDate>='2008-07-01' AND W.PayType=1 $TempDeliveryDate
											 ) A
											 ",$link_id));
$HZ668_Y+=$tmpForward_Y=sprintf("%.0f",$forwardY["Amount"]*$HKD_Rate);
$HZ668_W+=$tmpForward_W=sprintf("%.0f",$forwardW["Amount"]*$HKD_Rate);
$HZ668_A+=$tmpForward_A=sprintf("%.0f",$forwardY["Amount"]*$HKD_Rate)+sprintf("%.0f",$forwardW["Amount"]*$HKD_Rate);
*/
//总务费用

$Sum_Y+=$Value_Y[$Subscript][]=$HZ610_Y+$ZW6_Y+$ZW8_Y;								
$Sum_W+=$Value_W[$Subscript][]=$HZ610_W+$ZW6_W+$ZW8_W;								
$Sum_A+=$Value_A[$Subscript][]=$HZ610_A+$ZW6_Y+$ZW8_Y+$ZW6_W+$ZW8_W;	

//代付款项
$HZ668_Y+=$HZ704_Y+$HZ705_Y+$HZ724_Y;
$HZ668_W+=$HZ704_W+$HZ705_W+$HZ724_W;
$HZ668_A+=$HZ704_A+$HZ705_A+$HZ724_A;

//D69-624税款
$Sum_Y+=$Value_Y[$Subscript][]=$HZ624_Y+$HZ725_Y+$HZ726_Y+$HZ728_Y+$HZ729_Y;						
$Sum_W+=$Value_W[$Subscript][]=$HZ624_W+$HZ725_W+$HZ726_W+$HZ728_W+$HZ729_W;						
$Sum_A+=$Value_A[$Subscript][]=$HZ624_A+$HZ725_A+$HZ726_A+$HZ728_A+$HZ729_A;



//D70-643企业变更费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ643_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ643_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ643_A;
				
//D71-666培训费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ666_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ666_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ666_A;								

//D72-667软件费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ667_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ667_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ667_A;	

//D73-668代付款项
$Sum_Y+=$Value_Y[$Subscript][]=$HZ668_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ668_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ668_A;								

//D74-638餐费补助 
//$Sum_Y+=$Value_Y[$Subscript][]=$HZ638_Y;  					  $Sum_W+=$Value_W[$Subscript][]=$HZ638_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ638_A;								

//D75-637押金
$Sum_Y+=$Value_Y[$Subscript][]=$HZ637_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ637_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ637_A;								

//D76-636装修费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ636_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ636_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ636_A;								

//D79-678工伤费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ678_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ678_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ678_A;								

//D80-679消防费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ679_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ679_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ679_A;								

//D81-641借支
$Sum_Y+=$Value_Y[$Subscript][]=$HZ641_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ641_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ641_A;								

	
//意外保险
$checkYW_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='0' AND M.TypeId=3 $TempMonthtj",$link_id));
$checkYW_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='3' AND M.TypeId=3 $TempMonthtj",$link_id));

	//商业险
$checkSYX_Y=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='0' AND M.TypeId=4  $TempMonthtj",$link_id));
$checkSYX_W=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(M.cAmount+M.mAmount),0) AS Amount FROM $DataIn.sbpaysheet M WHERE 1 AND M.Month>='2008-07' AND M.Estate='3' AND M.TypeId=4  $TempMonthtj",$link_id));

$Sum_Y+=$Value_Y[$Subscript][]=sprintf("%.0f",$checkYW_Y["Amount"])+sprintf("%.0f",$checkSYX_Y["Amount"]);	
$Sum_W+=$Value_W[$Subscript][]=sprintf("%.0f",$checkYW_W["Amount"])+sprintf("%.0f",$checkSYX_W["Amount"]);	
$Sum_A+=$Value_A[$Subscript][]=sprintf("%.0f",$checkYW_Y["Amount"])+sprintf("%.0f",$checkYW_W["Amount"])+sprintf("%.0f",$checkSYX_Y["Amount"])+sprintf("%.0f",$checkSYX_W["Amount"]);


$Sum_Y+=$Value_Y[$Subscript][]=$HZ683_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ683_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ683_A;								//D78-683 财产/货物保险费
			


/*$Sum_Y+=$Value_Y[$Subscript][]=$HZ676_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ676_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ676_A;								//D77-676杰腾费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ677_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ677_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ677_A;								//D78-677华博装修费
$Sum_Y+=$Value_Y[$Subscript][]=$HZ680_Y;						$Sum_W+=$Value_W[$Subscript][]=$HZ680_W;						$Sum_A+=$Value_A[$Subscript][]=$HZ680_A;								//D78-677华博装修费*/

//研发费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ715_Y+$HZ716_Y+$HZ717_Y+$HZ718_Y+$HZ722_Y;
$Sum_W+=$Value_W[$Subscript][]=$HZ715_W+$HZ716_W+$HZ717_W+$HZ718_W+$HZ722_W;
$Sum_A+=$Value_A[$Subscript][]=$HZ715_A+$HZ716_A+$HZ717_A+$HZ718_A+$HZ722_A;

//制造费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ698_Y+$HZ703_Y+$HZ707_Y+$HZ691_Y;
$Sum_W+=$Value_W[$Subscript][]=$HZ698_W+$HZ703_W+$HZ707_W+$HZ691_W;
$Sum_A+=$Value_A[$Subscript][]=$HZ698_A+$HZ703_A+$HZ707_A+$HZ691_A;

//管理费用
$Sum_Y+=$Value_Y[$Subscript][]=$HZ727_Y+$HZ697_Y+$HZ732_Y;
$Sum_W+=$Value_W[$Subscript][]=$HZ727_W+$HZ697_W+$HZ732_W;
$Sum_A+=$Value_A[$Subscript][]=$HZ727_A+$HZ697_A+$HZ732_A;

//坏帐准备
$Sum_Y+=$Value_Y[$Subscript][]=$HZ733_Y;
$Sum_W+=$Value_W[$Subscript][]=$HZ733_W;
$Sum_A+=$Value_A[$Subscript][]=$HZ733_A;

//D68-不单独立项的项目归为其他：606/612/614/619/626/627/628/629/630/640/649/652/654/655/656/658/660/608/610/616/634/639/653/657/659/661/662/663/673

$HZ608_Y+=$HZ687_Y+$HZ686_Y;
$HZ608_W+=$HZ687_W+$HZ686_W;
$HZ608_A+=$HZ687_A+$HZ686_A;

$Sum_Y+=$Value_Y[$Subscript][]=$L0R68=
   $HZ606_Y+$HZ614_Y+$HZ619_Y+$HZ626_Y+$HZ627_Y+$HZ628_Y+$HZ629_Y+$HZ630_Y+$HZ640_Y+$HZ649_Y
+$HZ652_Y+$HZ654_Y+$HZ655_Y+$HZ656_Y+$HZ658_Y+$HZ660_Y +$HZ608_Y	+$HZ616_Y+$HZ634_Y	+$HZ639_Y	
+$HZ653_Y  +$HZ657_Y +$HZ659_Y+$HZ661_Y	+$HZ662_Y	+$HZ663_Y+$HZ719_Y+$HZ730_Y+$HZ723_Y;		
		
$Sum_W+=$Value_W[$Subscript][]=$L1R68=
   $HZ606_W+$HZ614_W+$HZ619_W+$HZ626_W+$HZ627_W+$HZ628_W+$HZ629_W+$HZ630_W+$HZ640_W+$HZ649_W
+$HZ652_W+$HZ654_W+$HZ655_W+$HZ656_W+$HZ658_W+$HZ660_W+$HZ608_W	+$HZ616_W +$HZ634_W+$HZ639_W	
+$HZ653_W +$HZ657_W +$HZ659_W+$HZ661_W+$HZ662_W+$HZ663_W+$HZ719_W+$HZ730_W+$HZ723_W;		
	
$Sum_A+=$Value_A[$Subscript][]=$L2R68=
  $HZ606_A+$HZ614_A+$HZ619_A+$HZ626_A+$HZ627_A+$HZ628_A+$HZ629_A+$HZ630_A+$HZ640_A+$HZ649_A
+$HZ652_A+$HZ654_A+$HZ655_A+$HZ656_A+$HZ658_A+$HZ660_A+$HZ608_A+$HZ616_A+$HZ634_A	+$HZ639_A	
+$HZ653_A+$HZ657_A +$HZ659_A	+$HZ661_A	+$HZ662_A+$HZ663_A+$HZ719_A+$HZ730_A+$HZ723_A;			

$SumType_Y[$Subscript][]=$Sum_Y;
$SumType_W[$Subscript][]=$Sum_W;
$SumType_A[$Subscript][]=$Sum_A;

$SumCol_Y[$Subscript]-=$Sum_Y; 
$SumCol_W[$Subscript]-=$Sum_W; 
$SumCol_A[$Subscript]-=$Sum_A; 

$SumOut_Y[$Subscript]+=$Sum_Y; 
$SumOut_W[$Subscript]+=$Sum_W; 
$SumOut_A[$Subscript]+=$Sum_A; 
if($Subscript>0){//数据写入当月行政费用：601 厂房租金；602水费；603电费；604宿舍租金；605宿舍水电费；607电话网络；国内快递费618；国外快递费；613差旅费；615交际费；609车辆费用；617银行手续费+汇款手续费；664证照费；665体验费；624税款；643企业变更费；678工伤费用；679消防费用
			$DataCheck1A[$Subscript]+=$HZ601_A+$HZ602_A+$HZ603_A+$HZ604_A+$HZ605_A+$HZ607_A+sprintf("%.0f",$checkKDF1_Y["Amount"])+sprintf("%.0f",$checkKDF1_W["Amount"])+$HZ618_A+sprintf("%.0f",$checkKDF2_Y["Amount"])+sprintf("%.0f",$checkKDF2_W["Amount"])+sprintf("%.0f",$checkKDF2_Y["Amount"])+sprintf("%.0f",$checkKDF2_W["Amount"])+$HZ613_A+$HZ615_A+$HZ609_A+$HZ617_A+sprintf("%.0f",$Sql51["Amount"])+$HZ664_A
			+$HZ665_A+sprintf("%.0f",$checkTJF_Y["Amount"])+sprintf("%.0f",$checkTJF_W["Amount"])+$HZ624_A+$HZ643_A+$HZ678_A+$HZ679_A;
			}
?>