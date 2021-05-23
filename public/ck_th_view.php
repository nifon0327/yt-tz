<?php 
include "../model/modelhead.php";
include "Ckth_Blue/config.php";  
require_once('../model/codefunjpg.php'); 
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Id=anmaOut($RuleStr1,$EncryptStr1,"f");
if ($Sign==1){
	 $thTableMain="ck12_thmain";
	 $thTableSheet="ck12_thsheet ";
	 $thTableReview="ck12_threview";
}
else{
     $thTableMain="ck2_thmain";
	 $thTableSheet="ck2_thsheet ";	
	 $thTableReview="ck2_threview";
}


if($Id>0){//单条记录的品捡报告
	include "ck_th_view_blue.php";
}

?>