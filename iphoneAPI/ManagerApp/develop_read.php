<?php 
//开发管理
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="develop";
 $info=explode("|", $info);
switch($dModuleId){
    case "main"://主页面
         switch($ModuleType){
	          case "SAVE":
	               include "develop/develop_updated.php";
	            break;
	          default:
	                 $GroupId=$info[1];
                     include "develop/develop_item_read.php";  
	            break;
         }
       break;
    case "Log":
          $Mid=$info[0];
          include "develop/develop_log_read.php"; 
          break;
	default:    
	    break;
}
?>