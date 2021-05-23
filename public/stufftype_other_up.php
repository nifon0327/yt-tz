<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-17
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件分类其它功能";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
switch($Action){
	case "1":
		$Log_Funtion="排序字母";
		$Result = mysql_query("SELECT Id,TypeName FROM $DataIn.StuffType order by Id DESC",$link_id);
		if($myRow = mysql_fetch_array($Result)){
			do {
				$Id=$myRow["Id"];
				$TypeName=$myRow["TypeName"];
				$chinese=new chinese;
				$Letter=substr($chinese->c($TypeName),0,1);
				//更新分类资料
				$upSql = "UPDATE $DataIn.StuffType SET Letter='$Letter' WHERE Id='$Id'";
				$upResult = mysql_query($upSql);
				}while ($myRow = mysql_fetch_array($Result));
			$Log="&nbsp;&nbsp;排序字母重整成功!</br>";
			}
		else{
			$Log="<div class=redB>&nbsp;&nbsp;排序字母重整失败!</div></br>";
			}
		break;
	case "2":
		$Log_Funtion="配件类型转换";
		$newStr=explode("|",$newTypeId);
		$newId=$newStr[0];
		$newName=$newStr[1];
		$oldStr=explode("|",$oldTypeId);
		$oldId=$oldStr[0];
		$oldName=$oldStr[1];
		$up_sql = "UPDATE $DataIn.stuffdata  SET TypeId='$newId' where TypeId='$oldId'";
		$up_result = mysql_query($up_sql);
		if($up_result){
			$Log="&nbsp;&nbsp;&nbsp;&nbsp;原属于 $oldName ( $oldId )的配件成功转为属于 $newName ( $newId )!</br>";
			}
		else{
			$Log="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;原属于 $oldName ( $oldId )的配件未能转为属于 $newName ( $newId )!</div></br>";
			$OperationResult="N";
			}
		break;
	case "3":
		$Log_Funtion="删除闲置分类";
		$Del_SQL = "DELETE A FROM $DataIn.StuffType A LEFT JOIN $DataIn.stuffdata B ON B.TypeId=A.TypeId WHERE B.TypeId IS NULL "; 
		$Del_Result = mysql_query($Del_SQL);
		if($Del_Result){
			$Log="&nbsp;&nbsp;&nbsp;&nbsp;清除闲置配件分类的操作成功!</br>";
			}
		else{
			$OperationResult="N";
			$Log="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;清除闲置配件分类的操作失败!</div></br>";
			}
		//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.StuffType");
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
