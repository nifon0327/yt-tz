<?php 
//公司简介
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="about";
switch($dModuleId){
   case "image":
       $NoEchoSign=1;
	   header("location: about/image/$info.png");
       break;
    case "main":
	default:
        include "about/about_item_read.php";
       break;
}
?>