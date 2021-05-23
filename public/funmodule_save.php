<?php  
//电信-joseph
//代码、数据库共享，加标识-EWEN
include "../model/modelhead.php";

//步骤2：
$Log_Item="功能模块";			//需处理
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
$Name=FormatSTR($Name);
//$Symbol=FormatSTR($Symbol);
//$Rate=FormatSTR($Rate);
$Date=date("Y-m-d");
$Sql = mysql_query("SELECT  MAX(ModuleId) AS abc FROM $DataPublic.funmodule order by ModuleId DESC",$link_id);
$ModuleId=mysql_result($Sql,0,"abc");
if($ModuleId==""){
	$ModuleId=1000+1;
	}
else{
	$ModuleId=$ModuleId+1;
	}

$KeyWebPage="";   //add by zx 2013-01-21为了避免权限传递有误而改的,权限设置转移到read_model_3.php中
if ($Parameter!="")
{
	$pos = strrpos($Parameter, ".php");
	if($pos !== FALSE){
		$substr1=substr($Parameter,0,$pos);
		//echo "$substr1";
		$pos1 = strrpos($substr1, "/");  //有些是直接文件名的，没有
		if($pos1 == FALSE){
			$pos1=-1;	
		}
		$KeyWebPage=substr($substr1,$pos1+1);
	}
}
	
$inRecode="INSERT INTO $DataPublic.funmodule (Id,cSign,ModuleId,ModuleName,Parameter,KeyWebPage,TypeId,OrderId,Estate,Locks,Date,Operator) VALUES (NULL,'7','$ModuleId','$ModuleName','$Parameter','$KeyWebPage','$TypeId','$OrderId','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
