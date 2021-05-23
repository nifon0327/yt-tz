<?php   
//电信-ZX  2012-08-01
$TestStandardSign=0;
switch($TestStandard){
	case 1://黄色通过 临时变动  已审核通过：订单临时要求改标准图
		$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
		if($checkteststandardRow = mysql_fetch_array($checkteststandard)){	
			$TestStandard="<span onClick='viewImage(\"$ProductId\",1,1)' style='CURSOR: pointer; color:#FF00FF; font-weight:bold' title='此订单需更改标准图'>$cName</span>";
			$TestStandardIpad = 3;
			}
		else{//正常已审核通过标准图
			//$TestStandard="<span class='yellowB' onClick='viewImage(\"$ProductId\",1,1)' style='cursor: pointer'>$cName</span>";
            if (is_file("../design/dwgFiles/$CId/Pord/$FileName")){
                $TestStandard = "<a href=\"../design/dwgFiles/$CId/Pord/$FileName\" target=\"download\">$cName&nbsp;<img src='../images/down.gif' style='$style' width='18' height='18'></a>";
            }else{
                $TestStandard = $cName;
            }
            $TestStandardSign=1;
			}
	break;
	case 2://蓝色 审核中
		$TestStandard="<div class='blueB' title='标准图审核中'>$cName</div>";
		$TestStandardIpad = 2;
	break;
	case 3://紫色#ff00ff  需更新标准图（对已通过的标准图做新的修改）
		$TestStandard="<div class='purpleB' style='CURSOR: pointer;' title='需更新标准图' onClick='viewImage(\"$ProductId\",1,1)'>$cName</div>";
		$TestStandardIpad = 3;
	break;
	case 4://紫色#FF6633 审核退回修改
		$checkRemark=mysql_fetch_array(mysql_query("select Remark FROM $DataIn.test_remark WHERE ProductId='$ProductId'",$link_id));
		$RemarkResult=$checkRemark["Remark"];
		$TestStandard="<div class='redB' style='CURSOR: pointer;' title='审核退回,原因:$RemarkResult' onClick='viewImage(\"$ProductId\",1,1)'>$cName</div>";
		$TestStandardIpad = 4;
	break;
	default://0未上传标准图
        if (is_file("../design/dwgFiles/$CId/Pord/$FileName")){
            $TestStandard = "<a href=\"../design/dwgFiles/$CId/Pord/$FileName\" target=\"download\">$cName&nbsp;<img src='../images/down.gif' style='$style' width='18' height='18'></a>";
        }else{
            $TestStandard = $cName;
        }
		break;
	}

//检查检讨报告
$CaseReport="&nbsp;";
$checkCaseSql=mysql_query("SELECT E.Picture,E.Title FROM $DataIn.casetoproduct C
LEFT JOIN $DataIn.errorcasedata  E ON E.Id=C.cId
WHERE C.ProductId='$ProductId' LIMIT 1
 ",$link_id);
if($checkCaseRow=mysql_fetch_array($checkCaseSql)){
	$CaseReport="<img src='../images/warn.gif' width='18' height='18'>";
	}
$CodeFile="&nbsp;";
$LableFile="&nbsp;";
$BoxFile="&nbsp;";
$WhiteFile="&nbsp;";
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
				case 4:$TypeSTR="白盒/坑盒";break;
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
					if($CodeType==4){
						$WhiteFile="<img src='../images/del.gif' alt='$AltStr' width='18' height='18'>";
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
					if($CodeType==4){
						$WhiteFile="<img src='../images/audit.gif' alt='$AltStr' width='18' height='18'>";
						}
					break;
				case 0://审核通过
					$AltStr=$TypeSTR."文件,用于车间自行打印";
					$CFile=anmaIn($CFile,$SinkOrder,$motherSTR);
					if($CodeType==1){
						//$CodeFile="<img onClick='OpenOrLoad(\"$dc\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						$CodeFile="<a href=\"openorload.php?d=$dc&f=$CFile&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' alt='$AltStr' width='18' height='18'></a>";
						}
					if($CodeType==2){
						//$LableFile="<img onClick='OpenOrLoad(\"$dc\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						$LableFile="<a href=\"openorload.php?d=$dc&f=$CFile&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' alt='$AltStr' width='18' height='18'></a>";
						}
					if($CodeType==4){
						//$WhiteFile="<img onClick='OpenOrLoad(\"$dc\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						$WhiteFile="<a href=\"openorload.php?d=$dc&f=$CFile&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' alt='$AltStr' width='18' height='18'></a>";
						}
					break;
				}
			}
		}while($checkCodeFileRow=mysql_fetch_array($checkCodeFileSql));
	}
$BoxFile="<a href='product_boxlable_print.php?p=$ProductId' target='_blank'><img src='../images/printer.gif' alt='打印外箱标签' width='18' height='18' border='0'></a>";
?>