<?php  
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="报关出口明细";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&CompanyId=$CompanyId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$DtateTemp=date("Ymd",strtotime($OrderDate));

//需锁定，防止同时读取生成重复号码
//$LockSql=" LOCK TABLES $DataIn.cw13_customsmain WRITE";$LockRes=@mysql_query($LockSql);

//检查并上传文件
if($DeclarationFile!=""){
	$FilePath="../download/DeclarationFile";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$FileType=substr("$DeclarationFile_name", -4, 4);
	$OldFile=$DeclarationFile;
	$PreFileName="D".$DeclarationNo.$FileType;
	$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
	}
$mainRecode="INSERT INTO $DataIn.cw13_customsmain    (Id,DeclarationNo,DeclarationEstate,DeclarationDate,CertificateNo,CertificateEstate,DeclarationCurrency,DeclarationAmount,exportinvoiceNo,exportinvoiceDate,BillNumber,DeclarationFile,CertificateFile,exportinvoiceFile,Remark,Estate,Locks,Date,Operator) VALUES 
(NULL,'$DeclarationNo','$DeclarationEstate','$DeclarationDate','$CertificateNo','1','2','$DeclarationAmount','',NULL,'','$uploadInfo','','','$Remark','1','0','$Date','$Operator')
";
//echo "$mainRecode";
$mainRes=@mysql_query($mainRecode);	
//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);

if($mainRes){
	$Log.="报关出口 $DeclarationNo 的资料添加成功<br>";
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
	}//end if($mainRes)
else{
	$Log="<div class=redB>&nbsp;&nbsp;报关出口 $DeclarationNo 的资料添加失败 $mainRecode </div>"; 
	$OperationResult="N";
	} 
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (Id,DateTime,Item,Funtion,Operator,Log,OperationResult) VALUES (NULL,'$DateTime','$Log_Item','$Log_Funtion','$Operator','$Log','$OperationResult')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
