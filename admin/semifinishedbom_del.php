<?php
include "../model/modelhead.php";

ChangeWtitle("$SubCompany  半成品配件BOM删除");
$fromWebPage="semifinishedbom_read";
$nowWebPage="semifinishedbom_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item=" 半成品配件BOM";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$PIdTemp=$checkid[$i];
	if ($PIdTemp!=""){
		$PIds=$PIds==""?$PIdTemp:($PIds.",".$PIdTemp);
		//记录旧的bom
		$gStuffId=$PIdTemp;
	    $checkVersion=mysql_fetch_array(mysql_query("SELECT MAX(VersionNo) AS VersionNo FROM $DataIn.semifinished_oldbom_main WHERE mStuffId='$gStuffId'",$link_id));
	    $VersionNo=$checkVersion['VersionNo'];
	    $VersionNo=$VersionNo==""?1.00:$VersionNo+0.10;
	    
	    $IN_recode="INSERT INTO $DataIn.semifinished_oldbom_main(Id,mStuffId,VersionNO,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'$gStuffId','$VersionNo','','1','0',CURDATE(),'$Operator')";
	    //echo $IN_recode;
	    $IN_res=@mysql_query($IN_recode);
	    $Mid=mysql_insert_id();
	    
	    $IN_recode2="INSERT INTO $DataIn.semifinished_oldbom_sheet(Id,Mid,mStuffId,StuffId,Relation,Date,Operator,creator,created) SELECT NULL,$Mid,mStuffId,StuffId,Relation,Date,Operator,creator,created FROM $DataIn.semifinished_bom WHERE mStuffId='$gStuffId'";
	    $IN_res2=@mysql_query($IN_recode2);
	    
	    $VersionNo=number_format($VersionNo,2);
	    $Log.="&nbsp;&nbsp;$gStuffId - 保存原半成品BOM记录,Version:$VersionNo; <br>";
	  }
	}

$DelSql = "DELETE FROM $DataIn.semifinished_bom WHERE mStuffId IN ($PIds)"; 
$DelResult = mysql_query($DelSql);
if($DelResult){
	$Log.="半成品配件ID：$PIds 的BOM关系解除成功<br>";
	}
else{
	$Log.="<div class=redB>半成品配件ID：$PIds 的BOM关系解除失败 $DelSql</div><br>";	
	$OperationResult="N";
	}
//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&CompanyId=$CompanyId&ProductType=$ProductType&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>