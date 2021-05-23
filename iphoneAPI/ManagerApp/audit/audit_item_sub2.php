<?php
 //行政费用审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1107",$modelArray)){
    $timeOut=getWorkLimitedTime(0,1107,$DataIn,$link_id);
    $AuditSign=$ActionArray["1107"];
    
    $SearchRows=" AND (M.WorkAdd<>6 OR S.Operator='50019' OR S.Operator='11903')";
    include "hz_qk_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"行政费用","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
            										   if (1) {
											        $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1107","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost2","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
											   } else {
		                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1107","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
											   }

    }
   
    $dataArray=array();$OverNums=0;$Nums=0;
    $SearchRows=" AND M.WorkAdd=6 AND S.Operator<>'11903' ";
    include "hz_qk_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                 "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"行政费用","RText"=>"陈忆甬"),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
            										   if (1) {
											        $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1107","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost2","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
											   } else {
		                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1107","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
											   }

    }
}

 //cz 车辆费用审核
$aModuleId=1595;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1595",$modelArray)){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray["1595"];
    
    include "carfee_m_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
              					  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"车辆费用","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost2","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

 //供应商税款审核
   $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array("1301",$modelArray)){
        $timeOut=getWorkLimitedTime(0,1301,$DataIn,$link_id);
        $AuditSign=$ActionArray["1301"];
        
        include "gys_sk_read.php";
        $Nums=count($dataArray);
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
                                      "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"供应商税款","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1301","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }
   
     //预付订金审核
   $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array("1048",$modelArray)){
       $timeOut=getWorkLimitedTime(0,1048,$DataIn,$link_id);
       $AuditSign=$ActionArray["1048"];
       
        include "cg_dj_read.php";
        $Nums=count($dataArray);
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
                                     "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"预付订金","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1048","head"=>$headArray,"Tag"=>"Cost","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }
   
   //其它收入审核
   $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array("1371",$modelArray)){
        $timeOut=getWorkLimitedTime(0,1371,$DataIn,$link_id);
        $AuditSign=$ActionArray["1371"];
         
        include "cw_otherin_read.php";
        $Nums=count($dataArray);
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
					                  "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"其他收入","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1371","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }	   
		   

//cz 快递费审核
    $aModuleId=1108;
    $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array("1108",$modelArray)){
        $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
        $AuditSign=$ActionArray["1108"];
        
        include "ch_express_read.php";
        $Nums=$itor;
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
                  					  "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"快递费","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"SbGjj","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }
   
//样品邮费审核
$aModuleId=1197;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("$aModuleId",$modelArray)){
     $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
     $AuditSign=$ActionArray["$aModuleId"];
     
    include "ch_samplemailing_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"样品邮费","RText"=>$timeOut[2]),
			                      //"Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost2","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}	

   
	
	 //cz 杂费审核
    $aModuleId=1051;
    $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array("1051",$modelArray)){
        $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
        $AuditSign=$ActionArray["1051"];
        
        include "cw_zf_read.php";
        $Nums=count($dataArray);
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
                  					  "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"杂费","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Cost2","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }
   
   	 //cz 免抵退审核
    $aModuleId=1436;
    $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array("1436",$modelArray)){
        $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
        $AuditSign=$ActionArray["1436"];
        
        include "cw_mdtax_read.php";
        $Nums=count($dataArray);
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
                  					  "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"免抵退","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }

?>