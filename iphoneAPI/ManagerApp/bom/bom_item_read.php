<?php 
//BOM采购管理
$jsondata=array();

//待采购
$COUNT_1=0;$COUNT_2=0;$COUNT_3=0;$COUNT_4=0;
$SearchBuyerId="";
$checkBranchId=mysql_query("SELECT BranchId FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND BranchId='4' AND Number NOT IN (10007)",$link_id);
 if (mysql_num_rows($checkBranchId)>0){
	 //只显示业务个人信息
	 $SearchBuyerId=$LoginNumber;
	 $editSign=1;
 }
//$SearchBuyerId=10161;
 if ($LoginNumber==10868 || $LoginNumber==10001 || $LoginNumber==10341 || $LoginNumber==10007) $editSign=1;

//待采购
if ($SegmentIndex==0 || $SegmentIndex==1){
		include "bom_item_sub_1.php";
}

//未收
if ($SegmentIndex==0 || $SegmentIndex==2){
		include "bom_item_sub_2.php";
}
/*
//特采
if ($SegmentIndex==0 || $SegmentIndex==3){
		include "bom_item_sub_3.php";
}

//未补
if ($SegmentIndex==0 || $SegmentIndex==4){
		include "bom_item_sub_4.php";
}		       

$SegmentIndex=$SegmentIndex>0?$SegmentIndex-1:0;
$SegmentArray=array("待采购($COUNT_1)","未收($COUNT_2)","特采($COUNT_3)","未补($COUNT_4)");
$SegmentIdArray=array("1","2","3","4");
*/
$SegmentIndex=$SegmentIndex>0?$SegmentIndex-1:1;
$SegmentArray=array("待采购($COUNT_1)","未收($COUNT_2)");
$SegmentIdArray=array("1","2");

$jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata); 

     
?>