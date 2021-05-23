<?php 
//授权书
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="authorize";
switch($dModuleId){
	 case "Main":
	       include "authorize/authorize_item_read.php";
	     break;
	case "Icon":
	      $NoEchoSign=1;
	      header("location: authorize/image/". $info . ".png");
	      break;
	default:
	    break;
}
?>