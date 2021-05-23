<?php 

//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="stuff";
$info=explode("|", $info);
switch($dModuleId){
   case "main": {

          include "stuff/stuff_type.php";
   }
         break;
	case "analyz": {
		switch ($sModuleId) {
			case "cg_six":
				include "stuff/stuff_cg_analyz.php";
				break;
			case "cg_chart":
			
				include "stuff/stuff_cg_chart.php";
				break;
			default:
				break;	
		}
	}
		break;
	case "Detail": {
		$StuffId=$info[0];
		include "report/stuff_detail_sheet.php";
	}
		break;
	case "list": {
		$typeid = $info[0];
		include "stuff/stuff_list.php";
	}
		break;
	case "add": {
		switch ($sModuleId) {
			case "pick": 
			{
				$info_0 = $info[0];
				$editStuffId = $info[1];
				include "stuff/stuff_edit_pick.php";
			}
				break;
			case "continue": 
			{
				include "stuff/stuff_continue.php";
			}
				break;
			case "search": 
			{
				include "stuff/stuff_muju_search.php";
			}
				break;
			case "searchName": 
			{
				include "stuff/stuff_name_search.php";
			}
				break;
			case "save": 
			{
				switch ($ModuleType) {
					case "ADD":
						include "stuff/stuff_save.php";
						break;
					case "Modify":
						include "stuff/stuff_modify.php";
						break;
					default: break;
				}
			}
				break;
			default:
				break;
			
		}
		
		
	}
		break;
	case "edit": {
		//@"delete":@"forbid"
		switch ($ActionId) {
			case "delete":
				include "stuff/stuff_delete.php";
				break;
			case "forbid":
				include "stuff/stuff_forbid.php"; 
				break;
			default:
				break;
		}
	}   break;
	default:
	    break;
}
?>