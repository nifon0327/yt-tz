<?php
	include "../model/modelhead.php";
	//步骤2：
	$Log_Item="追加 $chooseMonth 薪资记录";			//需处理
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_save";
	$_SESSION["nowWebPage"]=$nowWebPage;
	$Log_Funtion="保存";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$Date = date("Y-m-d");
	$DateTime=date("Y-m-d H:i:s");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	
	$checkHasSignListSql = "SELECT M.PayDate FROM  $DataIn.cwxzmain M 
					 LEFT JOIN $DataIn.cwxzsheet S ON S.Mid=M.Id 
					 WHERE S.Estate = 0 and S.Month = '$chooseMonth' Limit 1";
	$chechSignListResult = mysql_query($checkHasSignListSql);
	$checkSignListRow = mysql_fetch_assoc($chechSignListResult);
	
	$listDate = $checkSignListRow["Date"];
	if(strtotime($Date)-strtotime($listDate) > 5*24*3600)
	{
		/*
$checkOverDateSql = "Select A.Number, C.Name
							 Where $DataIn.cwxzsheet A
							 Left Join $DataPublic.staffmain C On C.Number = B.Number
							 Where A.Estate = '0'
							 And C.Estate = '1'
							 And A.Month = '$chooseMonth'";
*/
		
		$checkOverDateSqll = "SELECT A.Number, B.Name, C.sign, C.Date AS SignDate, F.PayDate AS listDate, A.Estate as payEstate
							  FROM $DataIn.cwxzsheet A
							  Left Join $DataIn.cwxzmain F On F.Id = A.Mid
							  LEFT JOIN $DataPublic.staffmain B ON A.Number = B.Number
							  LEFT JOIN (SELECT sign, Number, DATE FROM $DataPublic.wage_list_sign WHERE SignMonth =  '$chooseMonth') C ON C.Number = A.Number
							  WHERE A.Month = '$chooseMonth'
							  AND A.Estate IN (0) 
							  AND (C.sign IS NULL OR DATEDIFF( C.Date, F.PayDate ) >5 OR C.sign =  '')
							  AND B.Estate =  '1'
							 )";
		
		
		$checkOverDateResult = mysql_query($checkOverDateSqll);
		while($checkOverDateRow = mysql_fetch_assoc($checkOverDateResult))
		{
			$number = $checkOverDateRow["Number"];
			$name = $checkOverDateRow["Name"];
			$signDate = ($checkOverDateRow["SignDate"] == NULL)?"":$checkOverDateRow["SignDate"];
			$sign = $checkOverDateRow["sign"];
			$listDate = $checkOverDateRow["listDate"];
			//$payEstate = $checkOverDateRow["Estate"];
			
			$remark = "";
			$pay = 0;
			
			if($signDate == "")
			{
				$remark = "未签名<br>";
				$overDay = (strtotime($Date)-strtotime($listDate))/24/3600 - 5;
				$pay = $overDay * 10;
				
			}
			else if($signDate != "" && strlen($sign)==0)
			{
				$remark = "签名为空<br>";
				
			}
			else if((strtotime($signDate)-strtotime($listDate))/24/3600 - 5 > 0)
			{
				$overDay = (strtotime($signDate)-strtotime($listDate))/24/3600 - 5;
				$remark = "逾期签名 $overDay 天<br>";
				$pay = $overDay * 10;
				
			}
			
			if($remark != "")
			{
				$insertOverDate = "Insert Into $DataPublic.wage_sign_overtime (Id, Number, Month, PayMent, Remark, Estate) Values (NULL, '$number', '$chooseMonth', '$pay', ' ', '2')";
				$insertResult = mysql_query($insertOverDate);
				if($insertResult)
				{
					$Log .= "$name 逾期数据添加成功<br>";
				}
				else
				{
					$Log .= $insertOverDate."<br>";
				}
			}
			
		}
		
		
	}
	else
	{
		$Log = "未到逾期时间, 无法生成列表.";
	}
	
	include "../model/logpage.php";
	
?>