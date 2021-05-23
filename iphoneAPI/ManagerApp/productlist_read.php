<?php 
//產品目錄
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info

$info = explode("|", "$info");

switch($dModuleId){
	case "main":
	case "search":	
		include "productlist/productlist_item_read.php"; 	
		break;
		
	case "mainType": {

          include "productlist/productlist_type.php";
   }
         break;
	case "mainType": {

          include "productlist/productlist_type.php";
   }
         break;
	case "forbid": {
		switch ($sModuleId) {
			case "pick":
			include "productlist/productlist_forbid_pick.php";
			break;
			
			case "save":
			include "productlist/productlist_forbid_save.php";
			break;
			
			default : break;	
		}
		
	}
		break;
		 
	case "list": {
		$typeid = $info[0];
		include "productlist/productlist_list.php";
	}
	default:
	
		break;
}
?>