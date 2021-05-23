<?php 

$Log_Funtion="删除";

 	 $Log_Item="非bom说明书"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	
	 $OperationResult="N";
	 $Operator=$LoginNumber;
	 $Log = "";

	
	$editGoodsId = $info[0];
	$target_path1 = "../../download/nobom_intro/".$editGoodsId.".jpg";
	unlink($target_path1);
	$Log.= "$target_path1 已删除";
	$target_path1 = "../../download/nobom_intro/".$editGoodsId."_icon.jpg";
	unlink($target_path1);
	$Log.= "$target_path1 已删除";
	$target_path1 = "../../download/nobom_intro/".$editGoodsId."_icon_s.jpg";
	unlink($target_path1);
	$Log.= "$target_path1 已删除";
	$sqlRs = "update $DataPublic.nonbom4_goodsdata set Introduction = null where GoodsId=$editGoodsId";
	
	$InSql = @mysql_query($sqlRs);
	if ($InSql) {
		$OperationResult = "Y";
		$Log.= " 说明书 已删除";
	}

	$jsonArray = array(
				"ActionId" => "$ActionId",
				"Result" => "$OperationResult",
				"Info"=>"$infoSTR"
			);
			
			 
 $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log $infoSTR','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

?>