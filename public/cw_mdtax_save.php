<?php  
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="免抵退税明细";			//需处理
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
$DtateTemp=date("Ymd",strtotime($OrderDate));

//$LockSql=" LOCK TABLES $DataIn.cw14_mdtaxmain WRITE";$LockRes=@mysql_query($LockSql);
$FilePath="../download/cwmdtax";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
$FileType=".jpg";
//检查并上传文件
if($Attached!=""){
	$OldFile=$Attached;
	$PreFileName="M".$TaxNo.$FileType;
	$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
	}
	
if($proof!=""){
	$OldFile=$proof;
	$PreFileName="P".$TaxNo.$FileType;
	$uploadInfo1=UploadPictures($OldFile,$PreFileName,$FilePath);
	}
		
if($Taxgetdate==""){$Taxgetdate="0000-00-00";}
$endTax=$endTax==""?0:$endTax;
$mainRecode="INSERT INTO $DataIn.cw14_mdtaxmain (Id,TaxNo,PayDate,Taxdate,Taxamount,BankId,endTax,Taxgetdate,Attached,Proof,TaxIncome,Estate,Remark,Date,Operator,creator,created) VALUES 
(NULL,'$TaxNo','0000-00-00','$Taxdate','$Taxamount','$BankId','$endTax','$Taxgetdate','$uploadInfo','$uploadInfo1','0.00','1','$Remark','$Date','$Operator','$Operator',NOW())";
$mainRes=@mysql_query($mainRecode);	
//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);

if($mainRes){

	$Log.="免抵退税发票号 $TaxNo 的资料添加成功<br>";
	//将订单明细入库
	$x=1;
	$y=1;
	//=======================================加入报关费用
	$RecordCount=count($shipmainNumber);
	for($i=1;$i<=$RecordCount;$i++){	
		$thisshipmainNumber=$shipmainNumber[$i];
		$thisinvoiceNumber=$InvoiceNumber[$i];							
		if($thisshipmainNumber!=""){//第二步
			$sheetRecode="INSERT INTO $DataIn.cw14_mdtaxsheet (Id,TaxNo,shipmainNumber,InvoiceNumber,Estate,Date,Operator) 
			VALUES (NULL,'$TaxNo','$thisshipmainNumber','$thisinvoiceNumber','1','$Taxdate','$Operator')";
			//echo "<br> $sheetRecode";
			$sheetRes=@mysql_query($sheetRecode);
			if($sheetRes){
				$Log.="&nbsp;&nbsp; $i 出货流水号( $thisshipmainNumber) 的报关费用 添加成功<br>";	
				}
			else{
				$Log.="&nbsp;&nbsp; $i 出货流水号( $thisshipmainNumber) 的报关费用 添加成功<br>";	
				$OperationResult="N";
				}
			}////end if ($thisPid!="")
		}//end for
	//=====================================加入其他费用
		$RecordCount1=count($otherfeeNumber);
	    for($j=1;$j<=$RecordCount1;$j++){	
		$thisotherfeeNumber=$otherfeeNumber[$j];							
		if($thisotherfeeNumber!=""){//第二步
			$sheetRec="INSERT INTO $DataIn.cw14_mdtaxfee (Id,TaxNo,otherfeeNumber,Estate,Date,Operator) 
			VALUES (NULL,'$TaxNo','$thisotherfeeNumber','1','$Taxdate','$Operator')";
			//echo "<br> $sheetRecode";
			$sheetR=@mysql_query($sheetRec);
			if($sheetR){
				$Log.="&nbsp;&nbsp; $j Id号( $thisotherfeeNumber) 的行政费用 添加成功<br>";	
				}
			else{
				$Log.="&nbsp;&nbsp; $j Id号( $thisotherfeeNumber) 的行政费用 添加成功<br>";	
				$OperationResult="N";
				}
			}////end if ($thisPid!="")
		}//end for
			 
	}//end if($mainRes)
else{
	$Log="<div class=redB>&nbsp;&nbsp;免抵退税发票号 $TaxNo 的资料添加失败 $mainRecode </div>"; 
	$OperationResult="N";
	} 

//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (Id,DateTime,Item,Funtion,Operator,Log,OperationResult) VALUES (NULL,'$DateTime','$Log_Item','$Log_Funtion','$Operator','$Log','$OperationResult')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
