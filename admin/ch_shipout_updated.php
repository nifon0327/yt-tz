<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="提货资料";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
//$ALType="CompanyId=$CompanyId&Estate=$Estate";
//echo "ActionId:$ActionId <br>";
switch($ActionId){		
	case 26:
		$Log_Funtion="Invoice重置";
		include "billtopdf/ch_shipout_tobill.php";
		break;
	case 34://退回
	    $DelSql="DELETE M,S,P FROM $DataIn.ch1_deliverymain M 
		  LEFT JOIN $DataIn.ch1_deliverysheet S ON S.Mid=M.Id
		  LEFT JOIN $DataIn.ch1_deliverypacklist P ON P.Mid=M.Id
		  WHERE M.Id='$Id'";
		$DelResult=@mysql_query($DelSql);
		if($DelResult){
		     $Log.="记录退回成功,请重新生成提货单<br>";
		     }
		else{
		    $Log.="<div class='redB'>记录退回失败 $DelSql</div><br>";
			$OperationResult="N";
		    }
	 break;
	 case 934:
	     $DelSql="DELETE  FROM $DataIn.ch1_deliverysheet WHERE Id='$Id'";
		 $DelResult=mysql_query($DelSql);
	     break;
	case 936://删除附件
		$Log_Funtion="删除附件图片";
		$CheckImgSql=mysql_query("SELECT Mid,Picture FROM $DataIn.ch7_deliverypicture WHERE Id='$ImgId' LIMIT 1",$link_id);
		if($CheckImgRow=mysql_fetch_array($CheckImgSql)){
			$DelSql="DELETE FROM $DataIn.ch7_deliverypicture WHERE Id='$ImgId'";
			$DelResult=mysql_query($DelSql);
			if($DelResult){
				$Id=$CheckImgRow["Mid"];
				$Picture=$CheckImgRow["Picture"];
				$FilePath="../download/DeliveryNumber/$Picture";
				if(file_exists($FilePath)){
					unlink($FilePath);
					}
				}
				include "billtopdf/ch_shipout_tobill.php";
			}
		break;
	case 36:
	  $FilePath="../download/DeliveryNumber/";
	  $Log_Item="提货单附件";
			$Date=date("Y-m-d");
			$EndNumber=1;
			$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Picture) AS EndPicture FROM $DataIn.ch7_deliverypicture WHERE Mid='$Id'",$link_id));
			$EndFile=$checkEndFile["EndPicture"];
			if($EndFile!=""){
				$TempArray1=explode("_",$EndFile);
				$TempArray2=explode(".",$TempArray1[1]);
				$EndNumber=$TempArray2[0]+1;
				}
			$uploadNums=count($Picture);
			for($i=0;$i<$uploadNums;$i++){
				//上传文档				
				$upPicture=$Picture[$i];
				$Remark=$Remark[$i];
				$TempOldImg=$OldImg[$i];//原文件名
				$TempOldId=$OldId[$i];//原ID号
				if($upPicture!=""){	
					$OldFile=$upPicture;
					//检查是否有原档，如果有则使用原档名称，如果没有，则分配新档名
					if($TempOldImg!=""){
						$PreFileName=$TempOldImg;
						}
					else{
						$PreFileName=$Id."_".$EndNumber.".jpg";
						}
					//$uploadInfo=$PreFileName;
					$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
					if($uploadInfo!=""){
						if($TempOldImg==""){//写入记录
							$inRecode="INSERT INTO $DataIn.ch7_deliverypicture (Id,Mid,Remark,Picture,Date,Locks,Operator) VALUES (NULL,'$Id','$Remark','$uploadInfo','$Date','0','$Operator')";
							$inAction=@mysql_query($inRecode);
							if($inAction){
								$Log.="提货单 $Id 的附加文档 $uploadInfo 添加成功.<br>";
								$EndNumber++;}
							else{
								$Log.="<div class='redB'>提货单 $Id 的附加文档 $uploadInfo 添加失败. $inAction </div><br>";
								$OperationResult="N";
								}
							}
						else{				//更新记录
							$Log.="提货单 $Id 的附加文档 $uploadInfo 更新成功.<br>";
							$UpSql="UPDATE $DataIn.ch7_deliverypicture SET Remark='$Remark',Picture='$uploadInfo',Date=='$Date',Locks='0',Operator='$Operator')";
							$UpResult=mysql_query($UpSql);
							if($UpResult ){
								$Log.="提货单 $Id 的附加文档说明更新成功.<br>";
								}
							else{
								$Log.="<div class='redB'>提货单 $Id 的附加文档说明更新失败. $UpSql </div><br>";
								$OperationResult="N";
								}
							}
						}//end if($uploadInfo!="")
					}//end if($upPicture!="")
				}//end for($i=0;$i<$uploadNums;$i++)
			include "billtopdf/ch_shipout_tobill.php";
	   break;
	 default:
	    $Date=date("Y-m-d");
		$upDataSheet="$DataIn.ch1_deliverymain";
	    $SetStr="DeliveryNumber='$DeliveryNumber',DeliveryDate='$DeliveryDate',Remark='$Remark',ModelId='$ModelId',ForwaderId ='$ForwaderId'";
	    include "../model/subprogram/updated_model_3a.php";
		include "billtopdf/ch_shipout_tobill.php";
		if($OrderIds!=""){
		    $OrderArray=explode("|",$OrderIds);
			$count=count($OrderArray);
			for($i=0;$i<$count;$i++){
			   $TempArray=explode("^^",$OrderArray[$i]);
			   $POrderId=$TempArray[0];
			   $ShipId=$TempArray[1];
			   $DeliveryQty=$TempArray[2];
			   if ($DataIn=='ac'){
			         $InResult="INSERT INTO $DataIn.ch1_deliverysheet SELECT NULL,'$Id','$ShipId','$POrderId','$DeliveryQty',Price,Type,'1','0','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.ch1_shipsheet WHERE POrderId='$POrderId'";
			   }
				else{
				    $InResult="INSERT INTO $DataIn.ch1_deliverysheet SELECT NULL,'$Id','$ShipId','$POrderId','$DeliveryQty',Price,Type,'1','0' FROM $DataIn.ch1_shipsheet WHERE POrderId='$POrderId'";
               }
			  
			   $InRecode=mysql_query($InResult);
			   if($InRecode){
			            $j=$i+1;
			            $Log.="$j —流水号为： $POrderId 的订单添加成功!<br>";
			            }
				   else{
				         $Log.="$j —流水号为： $POrderId 的订单添加失败! $InResult <br>";
				      }
			   
		      }
		  }
	 break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
