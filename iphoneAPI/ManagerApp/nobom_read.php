<?php 
//个人助理项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="nobom";
$info=explode("|", "$info");
switch($dModuleId){
     case "intro": {
		 
		   switch ($sModuleId) {
			   case "repick": 
					include "nobom/introduction_edit_repick.php";
					break;   
					
				case "pick": 
					include "nobom/introduction_edit_pick.php";
					break;   
					
				case "add":
				 include "nobom/introduction_edit_add.php";
					break; 
				
					case "readd":
				 include "nobom/introduction_edit_readd.php";
					break; 
				
					case "upd":
				 include "nobom/introduction_edit_upd.php";
					break;
					
				case "delete":
				include "nobom/introduction_edit_delete.php";
				break;
				
					case "redelete": 
				include "nobom/introduction_edit_redelete.php";
				break;
				
				default :
				 	include "nobom/introduction_item_read.php"; 
					break;
		   }
         
		  
	 }
          break;
  	 
  
  	 default:
	 break;
}
?>