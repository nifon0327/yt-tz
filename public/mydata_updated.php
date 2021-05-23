<?php 
/*电信---yang 20120801
$DataPublic.staffmain
$DataPublic.staffsheet
$DataIn.usertable
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="资料";		//需处理
$upDataSheet="$DataIn.usertable";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
if($Action==1){
	$newPassword=MD5($newPassword);	
	$RePassword=MD5($RePassword);
	$oldPassword=MD5($oldPassword);
	$uNameSTR=$uName!=""?",uName='$uName'":"";
	$Sql = "UPDATE $upDataSheet SET Date='$Date',uPwd='$newPassword' $uNameSTR WHERE Number='$Login_P_Number' AND uPwd='$oldPassword' AND  '$newPassword'='$RePassword'";
	$Result = mysql_query($Sql);
	if ($Result){
		$Log="登录 $TitleSTR 成功! $Sql <br>";
		}
	else{
		$Log="<div class=redB>登录 $TitleSTR 失败! $Sql </div><br>";
		$OperationResult="N";
		}
	}
if($Action==2){
	$Sql=" UPDATE $DataPublic.staffmain M,$DataPublic.staffsheet S SET M.ExtNo='$ExtNo',M.Name='$Name',M.Nickname='$Nickname',M.Mail='$Mail',S.Dh='$Dh',S.Mobile='$Mobile' WHERE M.Number=S.Number AND M.Number='$Login_P_Number'";
	$Result = mysql_query($Sql);
	if ($Result){
		$Log.="人事 $TitleSTR 成功! $Sql <br>";
		}
	else{
		$Log.="<div class=redB>人事 $TitleSTR 失败! $Sql </div><br>";
		$OperationResult="N";
		}
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
