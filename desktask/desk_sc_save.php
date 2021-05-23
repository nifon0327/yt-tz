<?php   
//电信---yang 20120801
include "../model/modelhead.php";
$FileName="../download/codeandlable/".$ProductId."-".$CodeType.".qdf";
$Operator=$Login_P_Number;
foreach($_FILES as $f){
	//处理中文名
	//if (function_exists("iconv"))  $f[name] = iconv("UTF-8","GB2312",$f[name]);
	//检查是否已经存在同名文件
	//if (file_exists($f[name]))  header("HTTP/1.0 403");
	//保存文件
	move_uploaded_file($f["tmp_name"],$FileName);
	////////////////////////////////////////////////////////////////////////
	$Date=date("Y-m-d");
	//检查记录是否已经存在
	$OldFile=$CodeFile;
	$CheckFileSql=mysql_query("SELECT Id AS CId FROM $DataIn.file_codeandlable WHERE ProductId='$ProductId' AND CodeType='$CodeType'LIMIT 1",$link_id);
	if($CheckFileRow=mysql_fetch_array($CheckFileSql) && mysql_affected_rows()>0){//已经存在记录
		$CId=$CheckFileRow["Id"];
		$Relation_Sql = "UPDATE $DataIn.file_codeandlable SET Estate='2' WHERE Id='$CId'";		
		$Relation_Result = mysql_query($Relation_Sql);
		}
	else{//不存在记录,则为新增
		$inRecode="INSERT INTO $DataIn.file_codeandlable (Id,ProductId,CodeType,Date,Estate,Locks,Operator) VALUES (NULL,'$ProductId','$CodeType','$Date','2','0','$Operator')";
		$inAction=@mysql_query($inRecode);
		}
	///////////////////////////////////////////////////////////////////////
	echo "1";
}
?>