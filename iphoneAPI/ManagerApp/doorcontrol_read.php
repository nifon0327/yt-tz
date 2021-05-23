<?php 
//监控项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="doorcontrol";
$info=explode("|", "$info");
switch($dModuleId){
	 case "list":
	      $TypeId=$info[0];
	       include "doorcontrol/visitor_list.php";
	     break;
	 case "Badge":
	      include "submodel/tools_badge_read.php";
	    break;
	case "Type":
	     $PickModuleId="VisitorType";
	     include "submodel/pickname_read.php"; 
	    break;
	 case "SAVE":
	       include "doorcontrol/visitor_updated.php";
	       break;
	default:
	    break;
}
?>