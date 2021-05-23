<?php  
//电信-zxq 2012-08-01
//$DataIn.cw5_fbdh 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="货币汇兑记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Remark=FormatSTR($Remark);
//锁定表
//$LockSql=" LOCK TABLES $DataIn.cw5_fbdh WRITE";$LockRes=@mysql_query($LockSql);	
$Remark=trim($Remark);
$inRecode="INSERT INTO $DataIn.cw5_fbdh (Id,PayDate,OutBankId,OutCurrency,OutAmount,Rate,InBankId,InCurrency,InAmount,BillNumber,Bill,Remark,Locks,Operator) VALUES (NULL,'$PayDate','$OutBankId','$OutCurrency','$OutAmount','$Rate','$InBankId','$InCurrency','$InAmount','$BillNumber','0','$Remark','0','$Operator')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();

//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);

if ($inAction){ 
	$Log="$TitleSTR 成功! <br>";
	//上传图档
	if($Attached!=""){//有上传文件
		$OldFile=$Attached;
		$PreFileName="DH".$Id.".jpg";
		$FilePath="../download/fbdh/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;凭证上传成功！<br>";
			$sql = "UPDATE $DataIn.cw5_fbdh SET Bill='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;凭证上传失败！ </div><br>";
			$OperationResult="N";			
			}
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
		
		
	} 
else{
	$Log="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//解锁表
//$unLockSql="UNLOCK TABLES";	$unLockRes=@mysql_query($unLockSql);
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>