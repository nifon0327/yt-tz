<?php 
//证书
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="certificate";
switch($dModuleId){
	 case "Main":
	       include "certificate/certificate_item_read.php";
	     break;
	case "Icon":
	      $NoEchoSign=1;
	      header("location: certificate/image/EN_". $info . "_s.png");
	      break;
	default:
	    break;
}
?>