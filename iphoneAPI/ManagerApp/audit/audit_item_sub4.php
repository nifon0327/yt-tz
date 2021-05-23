<?php
$NextPage="END";
 	// 统计配件异动审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1591",$modelArray)){
   $timeOut=getWorkLimitedTime(0,1591,$DataIn,$link_id);
   $AuditSign=$ActionArray["1591"];
   
    include "cg_del_m.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"统计配件异动","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1591","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

 //订单锁定审核
$aModuleId=1524;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array($aModuleId,$modelArray)){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray["$aModuleId"];
    
    include "order_lock_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
              					  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"订单锁定","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

//配件锁定审核
$aModuleId=1525;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array($aModuleId,$modelArray)){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray["$aModuleId"];
    
    include "stuff_lock_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"配件锁定","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

//拆分订单审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1361",$modelArray)){
     $timeOut=getWorkLimitedTime(0,1361,$DataIn,$link_id);
     $AuditSign=$ActionArray["1361"];
     
    include "order_split_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"拆分订单","RText"=>$timeOut[2]),
			                      //"Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1361","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

//订单删除审核
$aModuleId=1356;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("$aModuleId",$modelArray)){
     $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
     $AuditSign=$ActionArray["$aModuleId"];
     
    include "order_del_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"订单删除","RText"=>$timeOut[2]),
			                      //"Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}	

//产品资料审核
$aModuleId=1261;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("$aModuleId",$modelArray)){
     $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
     $AuditSign=$ActionArray["$aModuleId"];
     
    include "productdata_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"产品资料","RText"=>$timeOut[2]),
			                      //"Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
    }
}	  

//客户退款审核
$aModuleId=1381;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("$aModuleId",$modelArray)){
     $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
     $AuditSign=$ActionArray["$aModuleId"];
     
    include "cg_tkout_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"客户退款","RText"=>$timeOut[2]),
			                      //"Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
               $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1268","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}	    
//}

 //配件名称审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1268",$modelArray)){
   $timeOut=getWorkLimitedTime(0,1268,$DataIn,$link_id);
   $AuditSign=$ActionArray["1268"];
   
    include "stuff_name_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              //$OverNums=$OverNums>0?$OverNums:"";
              $OverNums="";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"配件名称","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1268","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
    }
}		   
?>