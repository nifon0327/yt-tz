<?php
//$DataIn.pands 二合一已更新
include "../model/modelhead.php";

ChangeWtitle("$SubCompany 片材刀模保存");
$fromWebPage="slice_cutdie_read";
$nowWebPage="slice_cutdie_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$url="slice_cutdie_read";
$Log_Item="片材刀模设置";
$Log_Funtion="片材刀模设置";
$ALType="From=$From";
$dataArray=explode("|",$SIdList);
$Count=count($dataArray);
$x=1;
$Date=date("Y-m-d");
if($Count>0){
       $delSql  = "DELETE  FROM $DataIn.slice_cutdie  WHERE StuffId = $gStuffId ";
      $delResult = mysql_query($delSql);
}
for ($i=0;$i<$Count;$i++){
	        $cutId=$dataArray[$i];
			$checkResult  =  mysql_query("SELECT Id FROM $DataIn.slice_cutdie WHERE StuffId = $gStuffId and cutId=$cutId",$link_id);
			if(!$checkRow=mysql_fetch_array($checkResult)){
					//插入新的关系	
					$IN_recodeN="INSERT INTO $DataIn.slice_cutdie (Id,StuffId,cutId,Picture,Estate,Date,Operator) VALUES (NULL,'$gStuffId','$cutId','','2','$Date','$Operator')";
				    $resN=@mysql_query($IN_recodeN);
					if($resN){
					        $Log.="&nbsp;&nbsp; $x -刀模ID: $cutId 已设为片材配件 $gStuffId 的关系!</br>";
					}
					else{
						 $Log.="<div class='redB'>&nbsp;&nbsp; $x -刀模ID: $cutId 未设为片材配件 $gStuffId 的关系!</div></br>";
					} 
           }
	$x++;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>