<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="货币汇兑";		//需处理
$upDataSheet="$DataIn.cw5_fbdh";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=$Date==""?date("Y-m-d"):$Date;
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤3：需处理，更新操作
$x=1;
switch ($ActionId){
	case 7:
		$Log_Funtion="锁定";		$SetStr="Locks=0";						include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";		$SetStr="Locks=1";						include "../model/subprogram/updated_model_3d.php";		break;
	case "delCertificateNo"://
		$Log_Funtion="删除关联核销单号";//
		$sql = "delete FROM $DataIn.cw5_customsfbdh  WHERE CertificateNo='$CertificateNo'";
		$result = mysql_query($sql);
		if($result){
			$Log="汇兑记录关联核销单号 $CertificateNo 删除成功.</br>";
			}
		else{
			$Log="汇兑记录关联核销单号  $CertificateNo 删除失败! $sql</br>";
			$OperationResult="N";
			}
		break;	
	default:
		$PreFileName="DH".$Id.".jpg";
		$FilePath="../download/fbdh/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		if($Attached!=""){
			$OldFile1=$Attached;
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName,$FilePath);
			$BillSTR=$uploadInfo1==""?",Bill='0'":",Bill='1'";
			}
		if($BillSTR=="" && $oldAttached!=""){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath."/$PreFileName1";
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$BillSTR=",Bill='0'";
			}
		
		$Remark=FormatSTR($Remark);
		//$LockSql=" LOCK TABLES $DataIn.cw5_fbdh,$DataIn.cw5_customsfbdh WRITE";$LockRes=@mysql_query($LockSql);		
		$upSql = "UPDATE $DataIn.cw5_fbdh SET PayDate='$PayDate',OutCurrency='$OutCurrency',OutBankId='$OutBankId',OutAmount='$OutAmount',Rate='$Rate',InBankId='$InBankId',InCurrency='$InCurrency',InAmount='$InAmount',Remark='$Remark',Locks='0',Operator='$Operator',modifier='$Operator',modified='$DateTime'	$BillSTR WHERE Id=$Id";
		//echo "$upSql";
		$upResult = mysql_query($upSql);		
		if($upResult && mysql_affected_rows()>0){
			$Log="$TitleSTR 成功. <br>";									
			}
		else{
			$Log="<div class='redB'>$TitleSTR 失败! $upSql</div><br>";
			$OperationResult="N";
			}
			
			//将订单明细入库
		$x=1;
		$y=1;
		$RecordCount=count($CertificateNo);
		for($i=1;$i<=$RecordCount;$i++){	
			$thisCertificateNo=$CertificateNo[$i];							//产品ID
			if($thisCertificateNo!=""){//第二步
				$sheetRecode="INSERT INTO $DataIn.cw5_customsfbdh  (Id,BillNumber,CertificateNo,Estate,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$thisCertificateNo','1','0','$Date','$Operator')";
				//echo "<br> $sheetRecode";
				$sheetRes=@mysql_query($sheetRecode);
				if($sheetRes){
					$Log.="&nbsp;&nbsp; $i 的核销单号( $thisCertificateNo )添加成功<br>";	
					}
				else{
					$Log.="&nbsp;&nbsp; $i 的核销单号( $thisCertificateNo )资料添加失败！ </br>";
					$OperationResult="N";
					}
				}////end if ($thisPid!="")
			}//end for 					
		//$unLockSql="UNLOCK TABLES";	$unLockRes=@mysql_query($unLockSql);
	break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
	
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw5_fbdh,$DataIn.cw5_customsfbdh");
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>