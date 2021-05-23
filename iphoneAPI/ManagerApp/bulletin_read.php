<?php 
//通知
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$Params
$mModuleName="bulletin";
$info=explode("|", "$info");
switch($dModuleId){
	 case "main": //二级主页面
	       include "bulletin/bulletin_item_read.php";
	     break;
	 case "Detail"://详细信息
	      $Id=$info[0];
	      $Sign=$info[1];
	      include "bulletin/bulletin_detail_read.php";
	      break;
	 case "Image"://图片
	      $Id=$info[0];
	      include "bulletin/bulletin_image_read.php";
	      $NoEchoSign=1; 
	     break;
	default:
	    break;
}
?>