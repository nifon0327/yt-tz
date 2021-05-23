<?php 
//电信---yang 20120801
if($TestStandard==1){
	//输出标准图
	$FileName="T".$ProductId.".jpg";
	$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
	$td=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);
	//更改标准图 add by zx 20100804
	$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
	if($checkteststandardRow = mysql_fetch_array($checkteststandard)){	
		$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\",$ProductId,\"change\")' style='CURSOR: pointer; color:#FF00FF; font-weight:bold' title='需更改标准图!!'>$cName</span>";
		}
	else{
		$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\",$ProductId)' style='CURSOR: pointer;color:#FF6633;'>$cName</span>";
		}
	}
else{
	if($TestStandard==2){
		$TestStandard="<div class='blueB' title='标准图审核中'>$cName</div>";
		}
	else{
		$TestStandard=$cName;
		}
	}
//检查检讨报告
$CaseReport="&nbsp;";
$checkCaseSql=mysql_query("SELECT E.Picture,E.Title FROM $DataIn.casetoproduct C
LEFT JOIN $DataIn.errorcasedata  E ON E.Id=C.cId
WHERE C.ProductId='$ProductId' LIMIT 1
 ",$link_id);
if($checkCaseRow=mysql_fetch_array($checkCaseSql)){
	$CaseReport="<img src='../images/warn.gif' width='18' height='18'>";
	/*
	$Dir=download."/errorcase/";
	$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);
	do{
		$Picture=$checkCaseRow["Picture"];$File=anmaIn($Picture,$SinkOrder,$motherSTR);
		$Title=$checkCaseRow["Title"];
		$AltStr=$Title;
		.="<img src='../images/down.gif' onclick='OpenOrLoad(\"$Dir\",\"$File\")' alt='$AltStr' width='18' height='18'>&nbsp;&nbsp;&nbsp;";
		}while($checkCaseRow=mysql_fetch_array($checkCaseSql));
	*/
	}
$CodeFile="&nbsp;";
$LableFile="&nbsp;";
$BoxFile="&nbsp;";
$checkCodeFileSql=mysql_query("SELECT F.CodeType,F.Estate FROM $DataIn.file_codeandlable F
WHERE F.ProductId='$ProductId' ORDER BY F.CodeType",$link_id);
if($checkCodeFileRow=mysql_fetch_array($checkCodeFileSql)){
	//检查文件是否存在
	$dc=anmaIn("download/codeandlable/",$SinkOrder,$motherSTR);	
	do{
		$ext_Flag=0;
		$CodeType=$checkCodeFileRow["CodeType"];
		$CFile=$ProductId."-".$CodeType.".qdf";
		$CodeFileFilePath="../download/codeandlable/".$CFile;
			if(file_exists($CodeFileFilePath)){
			$ext_Flag=1;
		   }
		   else{
		      $CFile=$ProductId."-".$CodeType.".pdf";
		      $CodeFileFilePath="../download/codeandlable/".$CFile;
              if(file_exists($CodeFileFilePath)){
			      $ext_Flag=1;
		      }	 
		    }
		  if ($ext_Flag==1){
			switch($CodeType){
				case 1:$TypeSTR="背卡条码";break;
				case 2:$TypeSTR="白盒标签";break;
				}
			$CodeFileEstate=$checkCodeFileRow["Estate"];
			switch($CodeFileEstate){
				case 1://审核不通过
					$AltStr=$TypeSTR."文件审核未通过,请重新上传.";
					if($CodeType==1){
						$CodeFile="<img src='../images/del.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==2){
						$LableFile="<img src='../images/del.gif' alt='$AltStr' width='18' height='18'>";
						}
					break;
				case 2://审核中
					$AltStr=$TypeSTR."文件审核中";
					if($CodeType==1){
						$CodeFile="<img src='../images/audit.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==2){
						$LableFile="<img src='../images/audit.gif' alt='$AltStr' width='18' height='18'>";
						}
					break;
				case 0://审核通过
					$AltStr=$TypeSTR."文件,用于车间自行打印";
					$CFile=anmaIn($CFile,$SinkOrder,$motherSTR);
					if($CodeType==1){
						$CodeFile="<img onClick='OpenOrLoad(\"$dc\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==2){
						$LableFile="<img onClick='OpenOrLoad(\"$dc\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						}
					break;
				}
			}
		}while($checkCodeFileRow=mysql_fetch_array($checkCodeFileSql));
	}
$BoxFile="<a href='product_boxlable_print.php?p=$ProductId' target='_blank'><img src='../images/printer.gif' alt='打印外箱标签' width='18' height='18' border='0'></a>";
//echo "$BoxFile";
?>			