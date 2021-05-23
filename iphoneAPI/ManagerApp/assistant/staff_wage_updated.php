<?php 
$Log_Item="薪资签收";
 switch($ActionId){
        case "SAVE"://新增记录
         $Log_Funtion="签名";
         $Month=$info[0];
         $Strokes=$info[1];
         
         $Strokes=str_replace("^","|",$Strokes);
         $inRecode = "Replace Into $DataPublic.wage_list_sign (Id, Number, SignMonth, Sign, Date, Estate) Values (NULL, '$LoginNumber',  '$Month', '$Strokes', '$Date', '1')";
		 $inAction=@mysql_query($inRecode);
		if ($inAction){ 
		    $Log=$Log_Item .$Log_Funtion . "成功!<br>";
			$OperationResult="Y";
			$infoSTR=$Log_Funtion ."成功";
		}
		else
		{
		    $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
			$infoSTR=$Log_Funtion ."失败";
		}
        break;
}
?>