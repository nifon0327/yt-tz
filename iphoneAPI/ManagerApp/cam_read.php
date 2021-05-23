<?php 
//监控项目
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="cam";
switch($dModuleId){
     case "main":
          $postInfo=$info;
          include "cam/cam_item_read.php"; 
         break;
     case "newVideo":
	      $NoEchoSign=1;$checkId=$info;
	      include "cam/camvideo_read_new.php";
	     break;
	  //以下的将弃用 2014-06-05   
	 case "list":
	      $postInfo=$info;
	       include "cam/cam_list.php";
	     break;
	 case "video":
	      $NoEchoSign=1;
	      $idArray=explode(";", $info);
	      include "cam/camvideo_read.php";
	     break;
	default:
	    break;
}
?>