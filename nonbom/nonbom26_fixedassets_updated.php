<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="固定资产折旧";		//需处理
$upDataSheet="$DataPublic.nonbom7_fixedassets";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤3：需处理，更新操作
$x=1;
//echo 'ActionId:' . $ActionId;
switch($ActionId){
    case 17:
            $Log_Funtion="审核通过";		$SetStr="Estate=1,Locks=0";				
		    include "../model/subprogram/updated_model_3d.php";		
		
		
		 $inRecode="INSERT INTO $DataIn.nonbom7_fixedassets_audit (Checker,Reason,Sid,Estate,Locks,Date,creator,created) 
		 SELECT $Operator,'审核通过', Id,'1','0','$Date','$Operator','$DateTime' FROM  $upDataSheet WHERE Id IN ($Ids)";
		 $inAction=@mysql_query($inRecode);
        
        //添加折旧费用
         if ($OperationResult=="Y"){
               $curMonth = date("Y-m");
               $checkResult=mysql_query("SELECT S.BarCode,S.PostingDate,S.Amount,S.Depreciation,S.Salvage,C.GoodsId,
                        MAX(A.Month) AS Month,IFNULL(SUM(A.Times),0) AS Times,IFNULL(SUM(A.Amount),0) AS  DepreciationAmount 
                        FROM  $DataIn.nonbom7_fixedassets  S  
                        LEFT JOIN $DataIn.nonbom7_code C ON C.BarCode = S.BarCode  
                        LEFT JOIN $DataIn.nonbom7_depreciationcharge A ON A.BarCode=S.BarCode 
                        WHERE S.Id IN ($Ids)  GROUP BY S.Id",$link_id);
	           while($checkRow = mysql_fetch_array($checkResult)){
	                   $BarCode = $checkRow['BarCode'];
	                   $GoodsId = $checkRow['GoodsId'];
	                   $PostingDate = $checkRow['PostingDate'];
	                   $LastMonth    = $checkRow['Month'];
	                   $Amount        = $checkRow['Amount'];
	                   $Depreciation= $checkRow['Depreciation'];
	                   $Salvage= $checkRow['Salvage'];
	                   $Times= $checkRow['Times'];
	                   $DepreciationAmount = $checkRow['DepreciationAmount'];
	                   
	                   if ($LastMonth=="") $LastMonth=date('Y-m',strtotime($PostingDate));
	                   $m = 0; $IN_recodes="";
	                  
	                   while($LastMonth<$curMonth && $Times<$Depreciation){
	                        $Times++;
	                        $LastMonth = date('Y-m',strtotime("+1 month", strtotime($LastMonth . "-01")));
	                         
	                        if ($Times==$Depreciation){
	                               $chargeValue =$Amount*(1-$Salvage)-$DepreciationAmount;
	                        }else{
		                           $chargeValue =round($Amount*(1-$Salvage)/$Depreciation,2);
	                        }
	                        $IN_recodes.=$IN_recodes==""?"":",";
	                        $IN_recodes.="('$LastMonth','$BarCode','$GoodsId','$Depreciation','1','$chargeValue','$Date','$Operator')";
	                   }
	                   
	                  if ($IN_recodes!=""){
	                        $inRecodes= "INSERT INTO $DataIn.nonbom7_depreciationcharge(Month,BarCode,GoodsId,Depreciation,Times,Amount,Date,Operator) VALUES $IN_recodes;";
		                     $inAction=@mysql_query($inRecodes);
		                     if ($inAction){
			                      $Log.="资产编号为$BarCode 的记录新增 $Times 期的折旧费用成功!<br>"; 
		                     }else{
			                     $Log.="<div class='redB'>资产编号为$BarCode 的记录新增 $Times 期的折旧费用失败!<div>$inRecodes<br>"; 
		                     }
	                   }
	           }
         }
        
          $fromWebPage=$funFrom."_m";
         break;
   case 15:
        
	         $Log_Funtion="审核退回";	 $SetStr="Estate=3,Locks=1";		
			 include "../model/subprogram/updated_model_3d.php";	
			
			$inRecode="INSERT INTO $DataIn.nonbom7_fixedassets_audit (Checker,Reason,Sid,Estate,Locks,Date,creator,created) 
		 SELECT $Operator,'$ReturnReasons', Id,'3','0','$Date','$Operator','$DateTime' FROM  $upDataSheet WHERE Id IN ($Ids)";
		    $inAction=@mysql_query($inRecode);
       
            $fromWebPage=$funFrom."_m";
		break;
		
	case 3: //更新
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

 
	      $SetStr0=" GoodsNum='$GoodsNum' ";
	      
	      if ($rkId==0){
		       $SetStr0.=",Estate=$Estate"; 
	      }
	      if ($InvoiceFileName!=''){
	              $SetStr0.=",InvoiceFile=$InvoiceFileName";  
	      }
	      
	       if ($ContractFileName!=''){
	              $SetStr0.=",ContractFile=$ContractFileName";  
	      }
	      
	      $UpdateSql="UPDATE $DataIn.nonbom7_code  SET  $SetStr0  WHERE BarCode='$BarCode'";		
	      $UpdateResult=@mysql_query($UpdateSql);
	  
	       	
	     $DepreciationRow= mysql_fetch_array(mysql_query("SELECT Depreciation FROM $DataPublic.nonbom6_depreciation  
		          WHERE Id='$DepreciationId' LIMIT 1",$link_id));
	     $Depreciation = $DepreciationRow['Depreciation'];
	     
	     $checkResult = mysql_query("SELECT Id FROM $upDataSheet  WHERE BarCode='$BarCode' LIMIT 1",$link_id);
	     if($checkRow = mysql_fetch_array($checkResult)){
	           $Id = $checkRow['Id'];
	           
	           
	           $SetStr=" BranchId='$BranchId',PostingDate='$PostingDate',AddType='$AddType',
								DepreciationType='$DepreciationType',Salvage='$Salvage',Amount='$Amount',Estate='2',Remark='$Remark',
								Locks='1',modifier='$Operator',modified='$DateTime'";
			 
			 //如果未有折旧记录可修改折旧期数
			 $chargeRow= mysql_fetch_array(mysql_query("SELECT Id FROM $DataPublic.nonbom7_depreciationcharge   
		       WHERE BarCode='$BarCode' LIMIT 1",$link_id));
		      $SetStr.=$chargeRow['Id']==""?",DepreciationId='$DepreciationId',Depreciation='$Depreciation'":"";
		         				
			 $UpdateSql="UPDATE $upDataSheet  SET  $SetStr  WHERE Id ='$Id'";				
	       	$UpdateResult=@mysql_query($UpdateSql);
             if($UpdateResult && mysql_affected_rows()>0){
                  $Log.="ID 为$BarCode 的记录更新成功!<br>"; 
             }
             else{
                 $Log.="<div class='redB'>ID 为 $BarCode 的记录更新失败!</div><br>"; 
             }
	     }
	     else{
		       $inRecode = "INSERT INTO $upDataSheet (BarCode,PostingDate,BranchId,AddType,DepreciationType,DepreciationId,Depreciation,Salvage,Amount,Devalue,Remark,Estate,Date,Operator,creator,created) VALUES ('$BarCode','$PostingDate','$BranchId','$AddType','$DepreciationType','$DepreciationId','$Depreciation','$Salvage','$Amount','0','$Remark','2','$Date','$Operator','$Operator','$DateTime')";
		      $inAction=@mysql_query($inRecode);
              if ($inAction && mysql_insert_id()>0){ 
                     $Log.="ID 为$BarCode 的记录新增成功!<br>"; 
                }
             else{
                    $Log.="<div class='redB'>ID 为 $BarCode 的记录新增失败!</div><br>$inRecode<br>"; 
              }
	     }
	      $fromWebPage=$funFrom."_$From";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>