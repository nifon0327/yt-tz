<?php
 //请假审核
 if (in_array("1347",$modelArray) || in_array("1245",$modelArray) || ($test_cz  == 1)){
		        $timeOut=getWorkLimitedTime(0,1347,$DataIn,$link_id);//取得操作时间
		        $AuditSign=31;
		        
		        include "kq_qj_read.php";
		        $Nums=count($dataArray);
		        if ($Nums>0){
		                  $OverNums=$OverNums>0?$OverNums:"";
		                  $headArray=array(
		                                      "onTap"=>array("Value"=>"1"),
		                                      "RowSet"=>array("height"=>"30"), 
						                      "Title"=>array("Text"=>"请假"),
						                      "Col1"=>array("Text"=>"$OverNums"),
						                      "Col3"=>array("Text"=>"$Nums")
						                   ); 
		                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1347","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Leave","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
		        }
		   }
		   
//补休审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1533",$modelArray)){
    $timeOut=getWorkLimitedTime(0,1347,$DataIn,$link_id);//取得操作时间
    $AuditSign=31;
    
    include "kq_bx_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"补休"),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1533","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Leave","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

//cz 离职补助审核1598
$aModuleId=1598;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array($aModuleId,$modelArray)){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray[$aModuleId];
    
    include "staff_subsidy_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
              					  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"离职补助","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost2","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
    }
}
	 
//cz 保险款审核
$aModuleId=1161;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array($aModuleId,$modelArray)){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray[$aModuleId];
    
    include "sbgjj_m_read.php";
    $Nums=$itor;
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
              					  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"保险款","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"SbGjj","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

 //助学补助审核
$aModuleId=1520;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("$aModuleId",$modelArray)){
     $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
     $AuditSign=$ActionArray["$aModuleId"];
     
    include "childstudyfee_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"助学补助","RText"=>$timeOut[2]),
			                      //"Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost2","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

//工伤费审核
$aModuleId=1456;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array($aModuleId,$modelArray)){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray["$aModuleId"];
    
    include "staff_hurtfee_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
              					  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"工伤费","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Col4","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

//体检费审核
$aModuleId=1409;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array($aModuleId,$modelArray)){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray["$aModuleId"];
    
    include "staff_tjfee_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
              					  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"体检费","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost2","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}
?>