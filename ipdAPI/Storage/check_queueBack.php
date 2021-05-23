<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$Ids = explode("|", $_POST["Id"]);
	$CheckQtys = explode("|", $_POST["CheckQty"]);
	$CheckAQLs =explode("|",  $_POST["CheckAQL"]);
	$sumQtys = explode("|", $_POST["sumQty"]);
	$Login_P_Number = $_POST["Login_P_Number"];
	$reason = $_POST["reason"];
	$Date=date("Y-m-d");
	$Estate = "1";
	
	$FileType=".jpg";
	$FilePath="../../download/qcbadpicture/";
	if(!file_exists($FilePath))
	{
		makedir($FilePath);
	}               
		
	$Picture = "0";
	
	$backResult = "Y";
	$result = "";
	for($i=0; $i< count($Ids); $i++)
	{
		$Id = $Ids[$i]; 
		$sumQty = $sumQtys[$i];
		$CheckAQL = $CheckAQL[$i];
		$CheckQty = $CheckQtys[$i];
     if($DataIn=="ac"){
		  $inSql="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$sumQty','$CheckAQL','','$Estate','0','$Date','$Login_P_Number','0',null,null,null,null
          FROM  $DataIn.gys_shsheet WHERE Id='$Id' LIMIT 1";
		}else{
		  $inSql="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$sumQty','$CheckAQL','','$Estate','0','$Date','$Login_P_Number'
          FROM  $DataIn.gys_shsheet WHERE Id='$Id' LIMIT 1";
         }
		$inAction=@mysql_query($inSql,$link_id);
		$Mid=mysql_insert_id();
		if($Mid>0)
		{
			$insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '-1', '$sumQty', '$reason' ,'$Picture')";
			$insheetAction=@mysql_query($insheetSql,$link_id);
			if($insheetAction)
			{
				$updateShSQL = "UPDATE $DataIn.gys_shsheet SET Estate=1,Locks=1 WHERE Id='$Id'";
				// 插入备注
				$checkSql=mysql_query("SELECT Id FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
				if($checkRow=mysql_fetch_array($checkSql))
				{//更新
					$updateSQL = "UPDATE $DataIn.ck6_shremark SET Remark='$reason',Date='$Date',Operator='$Login_P_Number' WHERE ShId='$Id'";
					$updateResult = @mysql_query($updateSQL);
				}
				else
				{
					$addRecodes="INSERT INTO $DataIn.ck6_shremark (Id, ShId, Remark, Date, Operator) VALUES (NULL, '$Id', '$reason', '$Date', '$Login_P_Number')";
					$updateResult = @mysql_query($addRecodes);
				}	
				
			
				$updateShResult = mysql_query($updateShSQL,$link_id);
				if ($updateShResult && $updateResult)
				{
					$result .= "批量退回成功($Id)\n";
					
				}
				else
				{
					$backResult = "N";
					$result .= "批量退回失败($Id)\n";
					
				}
			}
		}
	}
	
	echo json_encode(array($backResult, $result));
	
?>