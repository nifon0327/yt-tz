<?php 
//EWEN 2013-02-20 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件资料";		//需处理
$upDataSheet="$DataPublic.nonbom4_goodsdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
    case 17://审核通过
		$Log_Funtion="审核名称";
		$SetStr="Estate=1";
		include "../model/subprogram/updated_model_3d.php";
		$fromWebPage=$funFrom."_m";
		break;
	case 34:
		$Log_Funtion="审核退回";
		$SetStr="Estate=3,ReturnReasons='$ReturnReasons'";
		include "../model/subprogram/updated_model_3d.php";
		$fromWebPage=$funFrom."_m";
		break;
   case "AddMaintainer":  //默认保养维修对象
             $ObjectStr="";
            if($PropertySign!=""){
                   if($PropertySign==2){//内部维修人
                                  $UpdateSql=" UPDATE  $upDataSheet  SET  WxNumber=$WxNumber  WHERE  GoodsId=$GoodsId";
                                  $ObjectStr="内部维修人";
                                 }
                   if($PropertySign==3){//外部维修公司
                                  $UpdateSql=" UPDATE  $upDataSheet SET  WxCompanyId=$WxCompanyId  WHERE  GoodsId=$GoodsId";
                                  $ObjectStr="外部维修公司";
                                 }
                   if($PropertySign==4){//内部保养人
                                  $UpdateSql=" UPDATE  $upDataSheet SET  ByNumber=$ByNumber  WHERE  GoodsId=$GoodsId";
                                  $ObjectStr="内部保养人";
                                 }
                   if($PropertySign==5){//外部保养公司
                                  $UpdateSql=" UPDATE  $upDataSheet SET  ByCompanyId=$ByCompanyId  WHERE  GoodsId=$GoodsId";
                                  $ObjectStr="外部保养公司";
                                 }
                         $UpdateResult=@mysql_query($UpdateSql);
                        if($UpdateResult &&  mysql_affected_rows()>0){
                                  $Log.="$ObjectStr 更新成功!";
                              }
                         else{
                                  $Log.="<span class='redB'>$ObjectStr 更新失败! $UpdateSql</span>";
                                  $OperationResult="N";
                                }
                }
         break;
      case "BomCompany":
                   $DelSql="DELETE  FROM  $DataPublic.nonbom4_bomcompany  WHERE  GoodsId=$GoodsId";
                   $DelResult=@mysql_query($DelSql);
                   if($mainCompanyId!=""){
                          $mainArray = explode("@", $mainCompanyId);
                          $mainNameArray = explode("@", $mainCompanyName);
                          $mainCount = count($mainArray);
                          for($i=0;$i<$mainCount;$i++){
                             $mCompanyId = $mainArray[$i];
                             $mCompanyName = $mainNameArray[$i];
	                         $IN_Sql="INSERT INTO $DataPublic.nonbom4_bomcompany(`Id`,`GoodsId`,`CompanyId`,`cSign`,`Estate`,`Locks`,`PLocks`,`creator`, `created`,`modifier`,`modified`,`Date`,`Operator`)VALUES(NULL,'$GoodsId','$mCompanyId','7','1','0','0','$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator')"; 
                            $IN_recode=@mysql_query($IN_Sql);
                            if($IN_recode && mysql_affected_rows()>0){
	                         		$Log.="关联包装BOM供应商 $mCompanyName 更新成功.<br>";
                             }
                        else{
                                  $Log.="<span class='redB'>关联包装BOM供应商 $mCompanyName 更新失败! $IN_Sql</span>";
                                  $OperationResult="N";
                             }
	                          
                          }

                      }
                  
                   if($ptsubCompanyId!=""){
                          $ptsubArray = explode("@", $ptsubCompanyId);
                          $ptsubNameArray = explode("@", $ptsubCompanyName);
                          $ptsubCount = count($ptsubArray);
                          for($i=0;$i<$ptsubCount;$i++){
                             $ptCompanyId = $ptsubArray[$i];
                             $ptCompanyName = $ptsubNameArray[$i];
                             $IN_Sql1="INSERT INTO $DataPublic.nonbom4_bomcompany(`Id`,`GoodsId`,`CompanyId`,`cSign`,`Estate`,`Locks`,`PLocks`,`creator`, `created`,`modifier`,`modified`,`Date`,`Operator`)VALUES(NULL,'$GoodsId','$ptCompanyId','3','1','0','0','$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator')";
                            $IN_recode1=@mysql_query($IN_Sql1);
                            if($IN_recode1 && mysql_affected_rows()>0){
	                         		$Log.="关联皮套BOM供应商 $ptCompanyName 更新成功.<br>";
                              }
                          else{
                                 $Log.="<span class='redB'>关联皮套BOM供应商 $ptCompanyName 更新失败! $IN_Sql</span>";
                                 $OperationResult="N";
                               }
                           }
                      }

            break;
    case 40://图片上传
            $FilePath="../download/nonbom/";
            
            $UpdateSTR = "";
            
		    $PreFileName1=$GoodsId.".jpg";
			if($Attached!=""){
					$OldFile1=$Attached;
					$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
					$UpdateSTR=$uploadInfo1==""?"":"Attached='1'";
			}
			
			$PreFileName2=$GoodsId."_s.png";
			if($AppIcon!=""){
					$OldFile2=$AppIcon;
					$uploadInfo2=UploadFiles($OldFile2,$PreFileName2,$FilePath);
					$UpdateSTR.=$uploadInfo2==""?"":($UpdateSTR==""?"AppIcon='1'":",AppIcon='1'");
			}
			
			if ($UpdateSTR!=''){
				    $updateSQL = "UPDATE $DataPublic.nonbom4_goodsdata SET  $UpdateSTR WHERE GoodsId='$GoodsId'";
		            $updateResult = mysql_query($updateSQL);
					if ($updateResult && mysql_affected_rows()>0){
						$Log="配件图片上传成功.<br>";
				   }
			}else{
				       $Log="<div class='redB'>未选择要上传的配件图片.</div><br>";
				    	$OperationResult="N";   
			}
           
           break;
	default:
		$FilePath="../download/nonbom/";
		$PreFileName1=$GoodsId.".jpg";
		if($Attached!=""){
			$OldFile1=$Attached;
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
			$AttachedSTR=$uploadInfo1==""?",Attached='0'":",Attached='1'";
			}
		if($AttachedSTR=="" && $oldAttached!=""){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath."/$PreFileName1";
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$AttachedSTR=",Attached='0'";
			}
		$GoodsName=FormatSTR($GoodsName) ;
		if ($OldGoodsName!=$GoodsName){
			$GoodsNameStr="GoodsName='$GoodsName',Estate='2',ReturnReasons='',";//需重新审核，审核原因清空
			}
		else{
			$GoodsNameStr="";
			}
$GetSign=$GetSign[0]==""?0:1;//模具配件 款项是否收回
$GetQty=$GetQty==""?0:$GetQty; //模具货款收回的条件 单位PCS

$DepreciationId=$DepreciationId==""?0:$DepreciationId;
$Salvage = $Salvage==""?0:$Salvage;
$AssetType = $AssetType==""?0:$AssetType;

		$SetStr="$GoodsNameStr Price='$Price',Unit='$Unit',TypeId='$TypeId',CkId='$CkId',nxId='$nxId',pdDate='$pdDate',ByNumber='$ByNumber',ByCompanyId='$ByCompanyId',WxNumber='$WxNumber',WxCompanyId='$WxCompanyId',Remark='$Remark',GetSign='$GetSign',GetQty='$GetQty',Date='$DateTime',Operator='$Operator',Locks='0',
		AssetType='$AssetType',DepreciationId='$DepreciationId',Salvage='$Salvage',brand='$brand' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
		
		//配件属性
     $tempCount=count($Property);
     $DelSql="DELETE FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId='$GoodsId' ";
     $DelResult=@mysql_query($DelSql);
       for($k=0;$k<$tempCount;$k++){
                   $inSql3="INSERT INTO $DataPublic.nonbom4_goodsproperty(Id,GoodsId,Property)VALUES(NULL,'$GoodsId','$Property[$k]')";
                   $inRes3=@mysql_query($inSql3);
        }


             //默认供应商的单价
      $defaultCount=count($checkdefaultId);
     $DelSql1="DELETE FROM $DataPublic.nonbom4_defaultcompany WHERE GoodsId='$GoodsId' ";
     $DelResult1=@mysql_query($DelSql1);
            for($k=0;$k<$defaultCount;$k++){
                     $TempCompanyId=$checkdefaultId[$k];
                     $TempPrice=$checkdefaultPrice[$k];
                     $IN_recode1="INSERT INTO $DataPublic.nonbom4_defaultcompany(Id,GoodsId,CompanyId,Price)VALUE(NULL,'$GoodsId','$TempCompanyId','$TempPrice')";       
                    $IN_res1=mysql_query($IN_recode1);
                }
		
      //关联BOM采购供应商
           if($BomCompany!=""){
                  $DelSql4="DELETE  FROM  $DataPublic.nonbom4_bomcompany  WHERE  GoodsId=$GoodsId";
                   $DelResult4=@mysql_query($DelSql4);    
                  $BomCompanyArray=explode("@", $BomCompany);
                  $BomCount=count($BomCompanyArray);
                   for($k=0;$k<$BomCount;$k++){
                            $tempArray=explode("~",$BomCompanyArray[$k]);
                           $BomCompanyId=$tempArray[0];
                           $cSign=$tempArray[2];
                           $IN_Sql4="INSERT INTO $DataPublic.nonbom4_bomcompany SELECT NULL,'$GoodsId','$BomCompanyId','$cSign'";
                           $IN_recode4=@mysql_query($IN_Sql4);
                    }
          }

		//更新库存表资料:默认供应商、最低库存
		$updateSQL = "UPDATE $DataPublic.nonbom5_goodsstock SET mStockQty='$mStockQty',CompanyId='$CompanyId' WHERE GoodsId='$GoodsId'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			$Log.="默认供应商或最低库存已更新.<br>";
			$OperationResult="N";
			}
		else{//如果更新失败,则加入该配件的库存资料，一般情况下用不到
			$inRecode="INSERT INTO $DataPublic.nonbom5_goodsstock (Id,GoodsId,wStockQty,oStockQty,mStockQty,CompanyId) VALUES (NULL,'$GoodsId','0','0','$mStockQty','$CompanyId')";
			$inRes=@mysql_query($inRecode);
			if($inRes && mysql_affected_rows()>0){
				$Log.="默认供应商或最低库存已更新.<br>";
				}
			else{
				$Log.="<div class='redB'>默认供应商或最低库存一更新失败或无需更新.</div><br>";
				$OperationResult="N";
				}
			}
			
    	if($ToolsId>0){
	    	$UpdateToolSql = "UPDATE $DataIn.fixturetool SET GoodsId ='$GoodsId' WHERE ToolsId='$ToolsId'";
	    	$UpdateToolResult = mysql_query($UpdateToolSql);
    	}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>