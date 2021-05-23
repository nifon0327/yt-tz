<?php 

$Log_Funtion="删除";

 	 $Log_Item="非bom说明书"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	
	 $OperationResult="N";
	 $Operator=$LoginNumber;
	 $Log = "";

	$editGoodsId = $info[0];

$ModifyTimes = 0;
$oldFile = $oldIcon = '';
	 $checkHasName = mysql_query("select Icon,File from $DataIn.studysheet where Id='$editGoodsId'");
	 if ($checkHasNameRow = mysql_fetch_array($checkHasName))
 {

	 $oldIcon = $checkHasNameRow['Icon'];
	 $oldFile = $checkHasNameRow['File'];

	 
 }	
 
	

	$target_path1 = "../../download/nobom_intro/".$oldIcon;
	unlink($target_path1);
	$Log.= "$target_path1 已删除";
	$target_path1 = "../../download/nobom_intro/".$oldFile;
	unlink($target_path1);
	$Log.= "$target_path1 已删除";
	$sqlRs = "delete from $DataPublic.studysheet where Id=$editGoodsId";
	
	$InSql = @mysql_query($sqlRs);
	if ($InSql) {
		$OperationResult = "Y";
		$Log.= " 培训说明 $editGoodsId 已删除";
	}

	$jsonArray = array(
				"ActionId" => "$ActionId",
				"Result" => "$OperationResult",
				"Info"=>"$infoSTR"
			);
			
			 
 $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log $infoSTR','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

?>