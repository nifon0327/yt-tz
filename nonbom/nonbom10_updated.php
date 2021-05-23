<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件报废记录";		//需处理
$upDataSheet="$DataIn.nonbom10_outsheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	 case 17://审核通过
		$Log_Funtion="审核";
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
	default:
		$changeQty=$Qty-$oldQty;//如果更新后的数量大于原数量，限报废增加，减少库存，且需要库存》新增的报废数量
		$updateSQL = "UPDATE $upDataSheet A
		LEFT JOIN $DataPublic.nonbom5_goodsstock B ON A.GoodsId=B.GoodsId
		SET A.Qty='$Qty',A.Remark='$Remark',A.Date='$DateTime',A.Operator='$Operator',A.Estate='2',A.Locks='0',
        B.wStockQty=B.wStockQty-'$changeQty',B.oStockQty=B.oStockQty-'$changeQty' WHERE A.Id='$Id' AND A.Locks='1' AND B.wStockQty>='$changeQty' 
        AND B.oStockQty>='$changeQty'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			$Log=$Log_Item.$Log_Funtion."成功.<br>";
			}
		else{
			$Log="<div class='redB'>".$Log_Item.$Log_Funtion."失败. $updateSQL</div><br>";
			$OperationResult="N";
			}
           $tempCount=count($BarCode);
           for($k=0;$k<$tempCount;$k++){
					$thisBarCode=$BarCode[$k];	
        			$thisbfRemark=$bfRemark[$k];	
     	   		    $thisPicture=$Picture[$k];	
                             $CheckRow=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.nonbom10_bffixed WHERE BarCode=$thisBarCode",$link_id));
                             $CheckId=$CheckRow["Id"];
                             if($CheckId==""){
                                     $IN_Sql="INSERT INTO $DataIn.nonbom10_bffixed(Id,BfId,GoodsId,BarCode,Picture,Estate,Date,Remark,Operator)
                                      VALUES(NULL,'$Id','$GoodsId','$thisBarCode','','1','$DateTime','$thisbfRemark','$Operator')";
                                     $IN_res=@mysql_query($IN_Sql);
                                     $thisMid=mysql_insert_id();
                                }
                            else{
                                        $IN_Sql="UPDATE $DataIn.nonbom10_bffixed SET Date='$DateTime' ,Remark='$thisbfRemark', Operator='$Operator' WHERE Id='$CheckId'";
                                        $IN_res=@mysql_query($IN_Sql);
                                         $thisMid=$CheckId;
                                      }
                              if($IN_res && mysql_affected_rows()>0){
           		                          $Log.="&nbsp;&nbsp;配件(固定资产)编号信息 $thisBarCode 报废成功!<br>";
                                         $UpdateSql="UPDATE  $DataIn.nonbom7_code SET Estate=0 WHERE BarCode=$thisBarCode"; $UpdateResult=@mysql_query($UpdateSql);
                                          if($thisPicture!=""){
	                                        	$FileType=".jpg";
	                                      	   $OldFile=$thisPicture;
	                                      	   $FilePath="../download/nonbombf/";
	                                         	if(!file_exists($FilePath)){
			                                          makedir($FilePath);
		                                      	}
		                                      $PreFileName="B".$thisMid.$FileType;
               		                          $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	                                        	if($Attached){
		                                                 $Log.="&nbsp;&nbsp;报废图片上传成功！$inRecode <br>";
		               	                                 $sql = "UPDATE $DataIn.nonbom10_bffixed SET Picture='$Attached' WHERE Id=$thisMid";
			                                             $result = mysql_query($sql);
			                                        }
		                                      else{
		                                            	  $Log.="<div class=redB>&nbsp;&nbsp;报废图片上传失败! </div><br>";	
		                                           	}
                                              }
                                     }
                              else{
		                     	  $Log.="<div class=redB>&nbsp;&nbsp;配件(固定资产)编号信息 $thisBarCode 报废失败 $IN_Sql </div><br>";	
                                     }
                          }
               //  }  

		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>