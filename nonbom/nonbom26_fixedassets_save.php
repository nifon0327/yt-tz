<?php 
//EWEN 2013-02-18 OK
include "../model/modelhead.php";
//步骤2：
$Log_Item="固定资产";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date =date("Y-m-d");

$Operator=$Login_P_Number;
$OperationResult="Y";

//上传发票
  $InvoiceFileName='';
 if($InvoiceFile!=''){
     $FilePath="../download/nonbom_cginvoice/";
     if(!file_exists($FilePath)){
	     makedir($FilePath);
     }
     $FileName=date('YmdHms').rand(100,999) . '.pdf';
     $uploadInfo=UploadFiles($InvoiceFile,$FileName,$FilePath);
     
	 $InvoiceFileName=$uploadInfo==""?'':"$FileName"; 

     if ($uploadInfo){
	     $Log="发票文件上传操作成功.$FileName <br>";
     }else{
	     $Log="<div class=redB>发票文件上传操作失败.$FileName </div><br>";
     }
}

//上传采购合同
$ContractFileName='';
 if($ContractFile!=''){
     $FilePath2="../download/nonbom_contract/";
     if(!file_exists($FilePath2)){
	     makedir($FilePath2);
     }
     $ContractFileName=date('YmdHms').rand(100,999) . '.pdf';
     $uploadInfo2=UploadFiles($ContractFile,$ContractFileName,$FilePath2);
     
	 $ContractFileName=$uploadInfo2==""?'':$ContractFileName; 

     if ($uploadInfo2){
	     $Log.="采购合同上传操作成功.$FileName <br>";
     }else{
	     $Log.="<div class=redB>采购合同上传操作失败.$FileName </div><br>";
     }
}

//if ($InvoiceFileName!=""){	        
			 $Remark=FormatSTR($Remark);
			
			$DepreciationRow= mysql_fetch_array(mysql_query("SELECT Depreciation FROM $DataPublic.nonbom6_depreciation  
					          WHERE Id='$DepreciationId' LIMIT 1",$link_id));
			$Depreciation = $DepreciationRow['Depreciation'];
			
			for($k=0;$k<$Qty;$k++){
			
				$MaxResult=mysql_fetch_array(mysql_query("SELECT  MAX(BarCode) AS MaxBarCode  FROM $DataIn.nonbom7_code",$link_id));
			     $MaxBarCode=$MaxResult["MaxBarCode"];
			     if($MaxBarCode=="")$MaxBarCode="8000000000001";
			     else $MaxBarCode=$MaxBarCode+1;
			     
			     $IN_Sql="INSERT INTO $DataIn.nonbom7_code(Id,rkId,GoodsId,BarCode,GoodsNum,CkId,TypeSign,Picture,Estate,Date,Operator)
			                               VALUES(NULL,'0','$GoodsId','$MaxBarCode','$GoodsNum','0','1','','$Estate','$DateTime','$Operator')";
			      $IN_res=mysql_query($IN_Sql);
			      if($IN_res && mysql_affected_rows()>0){
			                 $thisMid=mysql_insert_id();
			                 
				             $inRecode = "INSERT INTO $DataPublic.nonbom7_fixedassets  (BarCode,PostingDate,BranchId,AddType,DepreciationType,DepreciationId,Depreciation,Salvage,Amount,Devalue,InvoiceFile,ContractFile,Remark,Estate,Date,Operator,creator,created) VALUES ('$MaxBarCode','$PostingDate','$BranchId','$AddType','$DepreciationType','$DepreciationId','$Depreciation','$Salvage','$Amount','0','$InvoiceFileName','$ContractFileName','$Remark','2','$Date','$Operator','$Operator','$DateTime')";
					      $inAction=@mysql_query($inRecode);
				          if ($inAction && mysql_insert_id()>0){ 
				                 $Log.="&nbsp;&nbsp;配件编号信息 $MaxBarCode 新增成功!<br>";
				            }
				         else{
				               $Log.="<div class=redB>&nbsp;&nbsp;配件编号信息 $MaxBarCode 新增失败 $inRecode </div><br>";	
				          }
			         }
			         else{
			                  $Log.="<div class=redB>&nbsp;&nbsp;配件编号信息 $MaxBarCode 新增失败 $IN_Sql </div><br>";	
			         }             
			}
/*
}else{
	   $Log="<div class=redB>新增固定资产资料失败</div>";
}
*/
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
