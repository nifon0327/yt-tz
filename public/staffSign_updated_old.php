<?php
	
	include "../model/modelhead.php";
	//步骤2：
	ChangeWtitle("$SubCompany 更新薪资逾期扣款记录");//需处理
	$fromWebPage=$funFrom."_read";			
	$nowWebPage  =$funFrom."_updated";	
	$_SESSION["nowWebPage"]=$nowWebPage; 
	$Log_Item="薪资签收逾期扣款记录";
	$Log_Funtion = "更新";
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult = "Y";
	
	if($ActionId == 5 || $ActionId == 6)
	{
		for($i=0; $i<count($checkid); $i++)
		{
			$singleCheck = $checkid[$i];
				
			switch($ActionId)
			{
				case 5:
				{
					$Estate = "1";
				}
				break;
				case 6:
				{
					$Estate = "0";
				}
				break;
			}
		
			$overtimePaymentSql = "Update $DataPublic.wage_sign_overtime Set Estate = '$Estate' where Id = '$singleCheck'";
			$overtimePaymentResult = mysql_query($overtimePaymentSql);
			if($overtimePaymentResult)
			{
				$stateStr = ($Estate == "1")?"扣款记录成功":"扣款禁用成功";
				$Log = "$Name ($Number)".$stateStr."<br>";
			}
		
		}
	}
	else if($ActionId == 3)
	{
		$UpdatePayment = "Update $DataPublic.wage_sign_overtime Set PayMent = '$pay', Remark = '$Reason' Where Id = '$Id'";
		if(mysql_query($UpdatePayment))
		{
			$Log = "$Name ($Number)记录修改成功<br>";
		}
	}
	
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";
		
?>