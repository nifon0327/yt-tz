<?php   
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="半成品工序治工具";		
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
switch($ActionId){

   default :
      $x = 1;
      $DelSql = "DELETE FROM $DataIn.semifinished_tools WHERE mStuffId='$mStuffId'"; 
	  $DelResult = mysql_query($DelSql);
      $dataArray=explode("|",$SIdList);
	  $Count=count($dataArray);
	  for ($i=0;$i<$Count;$i++){
		 $tempArray=explode("^",$dataArray[$i]);
		 $ProcessId=$tempArray[0];
		 $toolsId=$tempArray[1];
		 $Relation=$tempArray[2];
		
		 //插入新的关系	
		 $IN_recodeN="INSERT INTO $DataIn.semifinished_tools (Id,mStuffId,ProcessId,ToolsId,Relation,Estate,Locks,
		 PLocks,creator, created,modifier,modified, Date, Operator)VALUES(NULL,'$mStuffId','$ProcessId','$toolsId',
		 '$Relation','1','0','0','$Operator','$DateTime','$Operator','$DateTime','$Date','$Operator')";
	     $resN=@mysql_query($IN_recodeN);
		 if($resN){  
		   $Log.="&nbsp;&nbsp; $x -配件ID号为 $mStuffId 的配件工序治工具设置成功!</br>";
		 }
		 else{
			  $Log.="<div class='redB'>&nbsp;&nbsp; $x -配件ID号为 $mStuffId 的配件工序治工具设置失败!$IN_recodeN</div></br>";
		 } 
		 $x++;
	 }
   break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>