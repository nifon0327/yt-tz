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
	
	switch ($ActionId) {
		case '5':{
			for($i=0;$i<count($checkid);$i++){
				$infoArray = explode('|', $checkid[$i]);
				$Number = $infoArray[0];
				$Month = $infoArray[1];
				$PayMent = $infoArray[2];
				$Estate = $PayMent=='0'?'0':'1';

				$isInsertSql = "SELECT * FROM $DataPublic.wage_sign_overtime WHERE Number=$Number and Month='$Month'";
				$isInsertResult = mysql_query($isInsertSql);
				if(mysql_num_rows($isInsertResult) == 0){
					$operationSql = "INSERT INTO $DataPublic.wage_sign_overtime (Id, Number, Month, PayMent, Remark, Estate) VALUES (NULL, $Number, '$Month', '$PayMent', '$Reason', $Estate)";
				}else{
					$operationSql = "UPDATE $DataPublic.wage_sign_overtime SET PayMent='$PayMent',Remark='$Reason', Estate=$Estate WHERE Number=$Number and Month='$Month'";
				}
				if(mysql_query($operationSql)){
					$Log .= "($Number)记录修改成功<br>";
				}else{
					$Log .= "($Number)记录修改成功<br>";
				}
			}
		}
		break;
		default:{
			$isInsertSql = "SELECT * FROM $DataPublic.wage_sign_overtime WHERE Number=$Number and Month='$Month'";
			$isInsertResult = mysql_query($isInsertSql);
			if(mysql_num_rows($isInsertResult) == 0){
				$operationSql = "INSERT INTO $DataPublic.wage_sign_overtime (Id, Number, Month, PayMent, Remark, Estate) VALUES (NULL, $Number, '$Month', '$PayMent', '$Reason', 2)";
			}else{
				$operationSql = "UPDATE $DataPublic.wage_sign_overtime SET PayMent='$PayMent',Remark='$Reason' WHERE Number=$Number and Month='$Month'";
			}
			// echo $operationSql;
			// exit();
			if(mysql_query($operationSql)){
				$Log = "$Name ($Number)记录修改成功<br>";
			}else{
				$Log = "$Name ($Number)记录修改成功<br>";
			}
		}
		break;
	}
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";
		
?>