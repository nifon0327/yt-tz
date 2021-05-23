<?php  
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="标准图图档";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"] = $nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
//上传图档
if($Attached!=""){//有上传文件
	$FType=substr("$Attached_name", -4, 4);
	$Ohycfile=$Attached;
	$FilePath="../download/standarddrawing/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$datelist=newGetDateSTR();
	$PreFileName=$datelist.$FType;
	$Attached=UploadFiles($Ohycfile,$PreFileName,$FilePath);
	if ($Attached!=""){		
		$inRecode="INSERT INTO $DataIn.doc_standarddrawing (Id,FileType,FileRemark,FileName,CompanyId,ProductType,Estate,Locks,Date,Operator) VALUES 
		(NULL,'$FileType','$FileRemark','$PreFileName','$CompanyId','$ProductType','1','0','$DateTime','$Operator')";
		$inAction=@mysql_query($inRecode);
		if($inAction){ 
			$Log="$TitleSTR 成功!<br>";
			} 
		else{
			$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
			$OperationResult="N";
			}
		}
	else{
		$Log="<div class='redB'>附件上传失败！</div><br>";
		$OperationResult="N";
		}
	}
//表解锁?
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
