<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch10_samplemail
$DataIn.cw10_samplemail
二合一已更新
*/
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="客户样品寄送资料";		//需处理
$upDataSheet="$DataIn.ch10_samplemail";	//需处理
$upDataMain="$DataIn.cw10_samplemail";	//需处理

$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="CompanyId=$CompanyId&chooseDate=$chooseDate";
//步骤3：需处理，更新操作
$x=1;
$FileDir="samplemail";
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 14:
		$Log_Funtion="请款";		$SetStr="Estate=2,Locks=0,OPdatetime='$DateTime'";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15://两种情况：审核或财务，如果是财务，要处理现金流水帐
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回
			$Log_Funtion="审核退回";			$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回
				$Log_Funtion="未结付退回";		$SetStr="Estate=1,Locks=1";		include "../model/subprogram/updated_model_3d.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=1,Locks=1";		include "../model/subprogram/updated_model_3c.php";
				}			
			}
		break;
	case 16:
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";		include "../model/subprogram/updated_model_3c.php";		break;
	case 17:
		$Log_Funtion="审核";		$SetStr="Estate=3,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 18://结付
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		break;
	case 20://财务更新
			//必选参数	:文件目录
			$Log_Funtion="主结付单资料更新";
			$Date=date("Y-m-d");
			$Estate=0;
			include "../model/subprogram/updated_model_cw.php";
		break;
	case 36:
		//删除之前的图片
		$FilePath="../download/$FileDir/";
		$Date=date("Y-m-d");
		$EndNumber=1;
		$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Picture) AS EndPicture FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id));
		$EndFile=$checkEndFile["EndPicture"];
		if($EndFile!=""){
			$TempArray1=explode("_",$EndFile);
			$TempArray2=explode(".",$TempArray1[1]);
			$EndNumber=$TempArray2[0]+1;
			}
		$uploadNums=count($Picture);
/////////////////////////////////////////////////
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
					$PreFileName="Sample".$Id."_".$EndNumber.".jpg";
					}
				$uploadInfo=$PreFileName;
				$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				if($uploadInfo!=""){
					if($TempOldImg==""){//写入记录
						$inRecode="INSERT INTO $DataIn.ch10_samplepicture (Id,Mid,Picture,Date,Locks,Operator) VALUES (NULL,'$Id','$uploadInfo','$Date','0','$Operator')";
						$inAction=@mysql_query($inRecode);
						if($inAction){
							$Log.="寄出样品图片 $uploadInfo 添加成功.<br>";
							$EndNumber++;}
						else{
							$Log.="<div class='redB'>寄出样品图片 $uploadInfo 添加失败. $inRecode </div><br>";
							$OperationResult="N";
							}
						}
					else{				//更新记录
						$Log.="寄出样品图片 $uploadInfo 更新成功.<br>";
						$UpSql="UPDATE $DataIn.ch10_samplepicture SET Picture='$uploadInfo',Date=='$Date',Locks='0',Operator='$Operator')";
						$UpResult=mysql_query($UpSql);
						if($UpResult ){
							$Log.="寄出样品图片 $uploadInfo 更新成功.<br>";
							}
						else{
							$Log.="<div class='redB'>寄出样品图片 $uploadInfo 更新失败. $UpSql </div><br>";
							$OperationResult="N";
							}
						}
					}//end if($uploadInfo!="")
				}//end if($upPicture!="")
			}//end for($i=0;$i<$uploadNums;$i++)
		break;
	case 936://删除附件
		$Log_Funtion="删除样品图片";
		$CheckImgSql=mysql_query("SELECT Mid,Picture FROM $DataIn.ch10_samplepicture WHERE Id='$ImgId' LIMIT 1",$link_id);
		if($CheckImgRow=mysql_fetch_array($CheckImgSql)){
			$DelSql="DELETE FROM $DataIn.ch10_samplepicture WHERE Id='$ImgId'";
			$DelResult=mysql_query($DelSql);
			if($DelResult){
				$Id=$CheckImgRow["Mid"];
				$Picture=$CheckImgRow["Picture"];
				$FilePath="../download/$FileDir/$Picture";
				if(file_exists($FilePath)){
					unlink($FilePath);
					}
				}
			}
		break;
	default:
		$Date=date("Y-m-d");
		$FilePath="../download/$FileDir/";
		//上传或删除图片
		$PreFileName1="Schedule".$Id.".jpg";//进度图片
		if($Schedule!=""){
			$OldFile1=$Schedule;
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);				
			$ScheduleSTR=$uploadInfo1==""?",Schedule='0'":",Schedule='1'";
			}
		if($ScheduleSTR=="" && $oldSchedule=="0"){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath.$PreFileName1;
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$ScheduleSTR=",Schedule='0'";
			}
		$SetStr="cSign='$cSign',SendDate='$theDate',DataType='$DataType',CompanyId='$theCompanyId',
		LinkMan='$LinkMan',ExpressNO='$ExpressNO',
		Pieces='$Pieces',Weight='$Weight',Qty='$Qty',Price='$Price',Amount='$Amount',PayType='$PayType',
		ServiceType='$ServiceType',HandledBy='$HandledBy',Description='$Description',
		Remark='$Remark',ReceiveDate='$ReceiveDate',Operator='$Operator' $ScheduleSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>