<?php 
//BOM采购管理
$jsondata=array();

//待采购
$COUNT_1=0;$COUNT_2=0;$COUNT_3=0;$COUNT_4=0;
$SearchBuyerId="";
$checkBranchId=mysql_query("SELECT BranchId FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND BranchId='4' AND Number NOT IN (10008)",$link_id);
 if (mysql_num_rows($checkBranchId)>0){
	 //只显示业务个人信息
	 $SearchBuyerId=$LoginNumber;
	 $editSign=1;
 }
//$SearchBuyerId=10161;
 if ($LoginNumber==10868 || $LoginNumber==10001 || $LoginNumber==10341 || $LoginNumber==10008) $editSign=1;

//未收
if ($SegmentIndex==0 || $SegmentIndex==1){
		include "supplier_item_sub_1.php";
}

//未付
if ($SegmentIndex==0 || $SegmentIndex==2){
		include "supplier_item_sub_2.php";
}

$SegmentIndex=$SegmentIndex>0?$SegmentIndex-1:0;
$SegmentArray=array("未收($COUNT_1)","未付($COUNT_2)");
$SegmentIdArray=array("1","2");

$jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata); 

     
?>