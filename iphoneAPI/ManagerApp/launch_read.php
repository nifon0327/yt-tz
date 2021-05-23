<?php 
//启动页加载
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="launch";
switch($dModuleId){
   case "image":
       $NoEchoSign=1;
	   header("location: launch/image/$info.png");
       break;
    case "main":
	default:
        include "launch/launch_item_read.php";
       break;
}
?>