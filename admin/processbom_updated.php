<?php   
//电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 加工工序BOM保存");
$fromWebPage="processbom_read";
$nowWebPage="processbom_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$url="cutbom_read";
$Log_Item="加工工序BOM";
$Log_Funtion="保存";
$ALType="From=$From";

$DelSql = "DELETE FROM $DataIn.process_bom WHERE StuffId='$gStuffId'"; 
$DelResult = mysql_query($DelSql);
$dataArray=explode("|",$SIdList);
$Count=count($dataArray);
$x=1;
$Date=date("Y-m-d");
for ($i=0;$i<$Count;$i++){
	$tempArray=explode("^",$dataArray[$i]);
	$ProcessId=$tempArray[0];
	$Relation=$tempArray[1];
	$BeforeProcessId=$tempArray[2];
	//插入新的关系	
	$IN_recodeN="INSERT INTO $DataIn.process_bom (Id,StuffId, ProcessId, Relation,BeforeProcessId, Date, Operator) VALUES (NULL,'$gStuffId','$ProcessId','$Relation','$BeforeProcessId','$Date','$Operator')";
	$resN=@mysql_query($IN_recodeN);
	if($resN){
		$Log.="&nbsp;&nbsp; $x -ID号为 $StuffId 的配件和ID号为 $ProcessId 的工序已加入加工工序BOM表中!</br>";
		}
	else{
		$Log.="<div class='redB'>&nbsp;&nbsp; $x -ID号为 $StuffId 的配件和ID号为 $ProcessId 的工序无法加入加工工序BOM表中!</div></br>";
		}
  $x++;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>