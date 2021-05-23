<?php
  //异常采单审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1046",$modelArray)){
    $timeOut=getWorkLimitedTime(0,1046,$DataIn,$link_id);
    $AuditSign=$ActionArray["1046"];
    
    include "cg_m_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"异常采单","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1046","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
  }
  
if (in_array("1245",$modelArray)){  
    $dataArray=array();$OverNums=0;$Nums=0;
    $timeOut=getWorkLimitedTime(0,10460,$DataIn,$link_id);
    $AuditSign=31;
    include "cg_replenish_read.php";
    $Nums=count($dataArray);
     if ($Nums>0){
		     $OverNums=$OverNums>0?$OverNums:"";
		    $headArray=array(
		                                  "onTap"=>array("Value"=>"1"),
		                                  "RowSet"=>array("height"=>"30"), 
					                      "Title"=>array("Text"=>"补料单","RText"=>$timeOut[2]),
					                      "Col1"=>array("Text"=>"$OverNums"),
					                      "Col3"=>array("Text"=>"$Nums")
					                   ); 
		             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"10460","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
       }
    
}

//配件退换审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1463",$modelArray)){
    $timeOut=getWorkLimitedTime(0,1463,$DataIn,$link_id);
    $AuditSign=$ActionArray["1463"];
    
    include "ck_th_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"配件退换","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1463","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}

//配件报废审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1135",$modelArray)){
    $timeOut=getWorkLimitedTime(0,1135,$DataIn,$link_id);
    $AuditSign=$ActionArray["1135"];
    
    include "ck_bf_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"配件报废","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1135","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}


//采单删除审核
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array("1269",$modelArray)){
   $timeOut=getWorkLimitedTime(0,1269,$DataIn,$link_id);
   $AuditSign=$ActionArray["1269"];
   
   $sumCGQty=0;
   include "cg_del_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $sumCGQty=number_format($sumCGQty);
              $headArray=array(
                                  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"采单删除","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$sumCGQty($Nums)")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1269","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}


//货款返利审核
$aModuleId=1413;
$dataArray=array();$OverNums=0;$Nums=0;
if (in_array($aModuleId,$modelArray)){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray["$aModuleId"];
    
    include "cg_fkhk_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
              					  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"货款返利","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Col4","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
} 

 //采购请款审核
   $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array("1047",$modelArray)){
        $timeOut=getWorkLimitedTime(0,1047,$DataIn,$link_id);
        $AuditSign=$ActionArray["1047"];
         
        include "cg_fk_read.php";
        $Nums=count($dataArray);
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
                                      "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"采购请款","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums","Color"=>"#0000FF"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"1047","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }

 //供应商扣款审核
    $aModuleId=1360;
    $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array($aModuleId,$modelArray)){
        $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
        $AuditSign=$ActionArray["$aModuleId"];
        
        include "cg_kk_read.php";
        $Nums=count($dataArray);
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
                  					  "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"供应商扣款","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }
   
    //cz 备品转入审核1620
    $aModuleId=1620;
    $dataArray=array();$OverNums=0;$Nums=0;
   if (in_array("1620",$modelArray)){
        $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
        $AuditSign=$ActionArray["1620"];
        
        include "ck_bp_read.php";
        $Nums=count($dataArray);
        if ($Nums>0){
                  $OverNums=$OverNums>0?$OverNums:"";
                  $headArray=array(
                  					  "onTap"=>array("Value"=>"1"),
                                      "RowSet"=>array("height"=>"30"), 
				                      "Title"=>array("Text"=>"备品转入","RText"=>$timeOut[2]),
				                      "Col1"=>array("Text"=>"$OverNums"),
				                      "Col3"=>array("Text"=>"$Nums")
				                   ); 
                 $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"1","data"=>$dataArray,"Page"=>"$NextPage");
        }
   }
		   
/*			
 //cz 扣款审核1359
$aModuleId=1359;
$dataArray=array();$OverNums=0;$Nums=0;
if (1){
    $timeOut=getWorkLimitedTime(0,$aModuleId,$DataIn,$link_id);
    $AuditSign=$ActionArray["1359"];
    if ($test_cz==1) {
		$AuditSign=31;
	}
    include "cw_kk_read.php";
    $Nums=count($dataArray);
    if ($Nums>0){
              $OverNums=$OverNums>0?$OverNums:"";
              $headArray=array(
              					  "onTap"=>array("Value"=>"1"),
                                  "RowSet"=>array("height"=>"30"), 
			                      "Title"=>array("Text"=>"扣款审核","RText"=>$timeOut[2]),
			                      "Col1"=>array("Text"=>"$OverNums"),
			                      "Col3"=>array("Text"=>"$Nums")
			                   ); 
             $jsonArray[]=array("ServerId"=>"$ServerId","ModuleId"=>"$aModuleId","head"=>$headArray,"hidden"=>"$onHidden","Tag"=>"Stuff","Reason"=>"0","data"=>$dataArray,"Page"=>"$NextPage");
    }
}  
*/	   
?>