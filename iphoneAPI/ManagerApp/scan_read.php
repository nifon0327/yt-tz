<?php 
//产品查询
//需上级main.php传入参数: $dModuleId,$sModuleId,$ActionId,$info
$mModuleName="scan";

$info=explode(",", $info);


switch($dModuleId){
    case "detail"://明细
      if (strlen($info[2])==13){
	      $POrderId='20' . substr($info[2],0,10);
      }else{
	      $POrderId= $info[2];
      }
      
      include "scan/porderid_check.php";
      break;
	default:
	    $ProductId=$info[4]; 
        include "scan/product_check.php";
       break;
}
?>