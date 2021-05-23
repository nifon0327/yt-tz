<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
//步骤2：
$Log_Item="非BOM采购预付订金";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$inRecode="INSERT INTO $DataIn.nonbom11_djsheet (Id,Mid,Did,CompanyId,PurchaseID,Amount,Remark,ReturnReasons,Date,Estate,Locks,Operator) VALUES (NULL,'0','0','$CompanyId','$PurchaseID','$Amount','$Remark','','$Date', '1', '1', '$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	if($Attached!=""){
			  $FilePath="../download/nonbomht/";
		         if(!file_exists($FilePath)){
						makedir($FilePath);
				}
				   $PreFileName="C".$Id.".jpg";
					$uploadInfo=UploadFiles($Attached,$PreFileName,$FilePath);
					if ($uploadInfo){
					    $Log.="上传凭证文件成功!<br>";
						 $upSql=@mysql_query("UPDATE $DataIn.nonbom11_djsheet  SET ContractFile='1' WHERE Id='$Id'");
					}
					else{
						 $Log.="<div class=redB>上传凭证文件失败!</div>$PreFileName<br>";
					}
		}      

	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
