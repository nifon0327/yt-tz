<?php 
//行事曆
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info

$mModuleName="Calendar";
$info=explode("|", "$info");
$powerArray=array(10001,10341,10868,11965,12171);//超级权限，可查看所有记录

switch($dModuleId){
	case "main":	
		include "calendar/calendar_item_read.php"; 	
		break;
	case "list":
		$selectDate = $info[0];
		include "calendar/calendar_list.php";
		break;
	case "pick":
		include "calendar/calendar_pick_read.php";
		break;
	case "SAVE":
		$Log_Item="";
		$Date=date("Y-m-d");$DateTime=date("Y-m-d H:i:s");
		$OperationResult="N";
		$Operator=$LoginNumber;

		include "calendar/calendar_updated.php";
		
		if ($Log_Item != "") {
			$IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) 
							VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$LoginNumber')";
			$IN_res = @mysql_query($IN_recode);
			$jsonArray = array(
				"ActionId" => "$ActionId",
				"Result" => "$OperationResult",
				"Info"=>"$infoSTR"
			);
		}
		break;
	case "search":
		include "calendar/calendar_search.php";
		break;
	case "image":
		include "calendar/calendar_image.php";
		break;
		
		
	case "Record":
	
				$eventId = $info[0]; $recordId = $info[1]; 
				
				$oldInfo = mysql_fetch_assoc(mysql_query("select Id from $DataPublic.event_recordsheet where EventId=$eventId "));
	 $recordId = $oldInfo["Id"];
	
				
				if ($recordId>0) {
					include "calendar/calendar_record_modify.php";
				} else {
					include "calendar/calendar_record_updated.php";
				}
			
		break;
	
	case "delete":
		$eventId = $info[0];
		include "calendar/calendar_record_delete.php";
		break;
	case "share":
	
	{
		switch ($sModuleId) {
			case "pick":
			include "calendar/calendar_share_pick.php";
			break;
			
			case "save":
			$recordId = $info[0];
			$shareWith = $info[1];
			include "calendar/calendar_share_save.php";
			break;
			
			default:
			break;
		}
		
		
	}
		break;
	default:
	
		break;
}
?>