<?php 
/*
//电信-zxq 2012-08-01
更新:加入生产记录的处理
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="报关出口";		//需处理
$upDataSheet="$DataIn.cw13_customsmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId";
//步骤3：需处理，更新操作
$x=1;
//echo "ActionId:$ActionId";
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
		break;
	/*
	case 17:
		$Log_Funtion="核实";		
		
		
		$SetStr="Estate=3,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	*/	
	case "delshipmainNumber"://
		$Log_Funtion="删除关联Invoice";//
		$sql = "delete FROM $DataIn.cw13_customssheet  WHERE shipmainNumber='$shipmainNumber'";
		$result = mysql_query($sql);
		if($result){
			$Log="报关出口关联Invoice出货流水号 $shipmainNumber 删除成功.</br>";
			}
		else{
			$Log="报关出口关联Invoice出货流水号  $shipmainNumber 删除失败! $sql</br>";
			$OperationResult="N";
			}
		break;
		
	case 87:
		$FilePath="../download/DeclarationFile/";
		echo "$DeclarationFile   》  $CertificateFile 》 $exportinvoiceFile";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		//上传或删除文件
		if($DeclarationFile!=""){	//有上传文件
			$FileType=substr("$DeclarationFile_name", -4, 4);
			$OldFile=$DeclarationFile;
			$PreFileName="D".$DeclarationNo.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			$DeclarationFileValue=$uploadInfo==""?"":",DeclarationFile='$PreFileName'";
			
			}
		else{					//没有上传文件
			if($delDeclarationFile!=""){//已选取删除原文件
			$DelFilePath=$FilePath.$delDeclarationFile;
			if(file_exists($DelFilePath)){
				unlink($DelFilePath);
				$DeclarationFileValue=",DeclarationFile=''";
				}			
			}
		}



		//上传或删除文件
		if($CertificateFile!=""){	//有上传文件
			$FileType=substr("$CertificateFile_name", -4, 4);
			$OldFile=$CertificateFile;
			$PreFileName="C".$CertificateNo.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			$CertificateFileValue=$uploadInfo==""?"":",CertificateFile='$PreFileName'";
			
			}
		else{					//没有上传文件
			if($delCertificateFileFile!=""){//已选取删除原文件
			$DelFilePath=$FilePath.$delCertificateFileFile;
			if(file_exists($DelFilePath)){
				unlink($DelFilePath);
				$CertificateFileValue=",CertificateFile=''";
				}			
			}
		}

		//上传或删除文件
		if($exportinvoiceFile!=""){	//有上传文件
			$FileType=substr("$exportinvoiceFile_name", -4, 4);
			$OldFile=$exportinvoiceFile;
			$PreFileName="E".$exportinvoiceNo.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			$exportinvoiceFileValue=$uploadInfo==""?"":",exportinvoiceFile='$PreFileName'";
			
			}
		else{					//没有上传文件
			if($delexportinvoiceFile!=""){//已选取删除原文件
			$DelFilePath=$FilePath.$delexportinvoiceFile;
			if(file_exists($DelFilePath)){
				unlink($DelFilePath);
				$exportinvoiceFileValue=",exportinvoiceFile=''";
				}			
			}
		}	
		//1主单信息更新
		$mainSql = "UPDATE $DataIn.cw13_customsmain SET Remark=Remark 
		$DeclarationFileValue  $CertificateFileValue  $exportinvoiceFileValue   WHERE Id='$Id'";
        echo "$mainSql";
		$mainResult = mysql_query($mainSql);
		if ($mainResult){
			$Log="报关出口Id为 $Id 的资料已经更新.<br>";
	
		}
		else{
			$Log="<div class=redB>报关出口Id为 $Id 的资料更新失败 $mainSql </div>"; 
			$OperationResult="N";
		}
		//2.数量处理		

		break;

	default://更新订单资料OK
		$FilePath="../download/DeclarationFile/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		//上传或删除文件
		if($DeclarationFile!=""){	//有上传文件
			$FileType=substr("$DeclarationFile_name", -4, 4);
			$OldFile=$DeclarationFile;
			$PreFileName="D".$DeclarationNo.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			$FileValue=$uploadInfo==""?"":",DeclarationFile='$PreFileName'";
			
			}
		else{					//没有上传文件
			if($delFile!=""){//已选取删除原文件
			$DelFilePath=$FilePath.$delFile;
			if(file_exists($DelFilePath)){
				unlink($DelFilePath);
				$FileValue=",DeclarationFile=''";
				}			
			}
		}
		if(($exportinvoiceDate!="") && ($exportinvoiceNo!="")){
			$DateValue=",exportinvoiceDate='$exportinvoiceDate'";
		}
		//1主单信息更新
		$mainSql = "UPDATE $DataIn.cw13_customsmain SET DeclarationEstate='$DeclarationEstate',DeclarationDate='$DeclarationDate',CertificateNo='$CertificateNo',CertificateEstate='$CertificateEstate',
	DeclarationAmount='$DeclarationAmount',exportinvoiceNo='$exportinvoiceNo' $DateValue,Remark='$Remark'
		$FileValue WHERE Id='$Id'";
		/*
		echo  "UPDATE $DataIn.cw13_customsmain SET DeclarationDate='$DeclarationDate',CertificateNo='$CertificateNo',CertificateEstate='$CertificateEstate',
	DeclarationAmount='$DeclarationAmount',exportinvoiceNo='$exportinvoiceNo' $DateValue,Remark='$Remark'
		$FileValue WHERE Id='$Id'";
		*/
		$mainResult = mysql_query($mainSql);
		if ($mainResult){
			$Log="报关出口Id为 $Id 的资料已经更新.<br>";
			//将订单明细入库
			$x=1;
			$y=1;
			$RecordCount=count($shipmainNumber);
			for($i=1;$i<=$RecordCount;$i++){	
				$thisshipmainNumber=$shipmainNumber[$i];							//产品ID
				if($thisshipmainNumber!=""){//第二步
					$sheetRecode="INSERT INTO $DataIn.cw13_customssheet (Id,DeclarationNo,shipmainNumber,Estate,Locks,Date,Operator) VALUES (NULL,'$DeclarationNo','$thisshipmainNumber','1','0','$Date','$Operator')";
					//echo "<br> $sheetRecode";
					$sheetRes=@mysql_query($sheetRecode);
					if($sheetRes){
						$Log.="&nbsp;&nbsp; $i Invoice 的出货流水号( $thisshipmainNumber )添加成功<br>";	
						}
					else{
						$Log.="&nbsp;&nbsp; $i Invoice 的出货流水号( $thisshipmainNumber )资料添加失败！</br>";
						$OperationResult="N";
						}
					}////end if ($thisPid!="")
				}//end for 			
		}
		else{
			$Log="<div class=redB>报关出口Id为 $Id 的资料更新失败 $mainSql </div>"; 
			$OperationResult="N";
		}
		//2.数量处理
		
		break;
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw13_customsmain,$DataIn.cw13_customssheet");
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>