<?php 

$KeyWebPage=$nowWebPage;  //一般传递 $nowWebPage 与  $KeyWebPage 相等，权取 add by zx 2013-01-21
@$Keys=$_POST['Keys'];
if((!isset($Keys)) || empty($Keys)) { //如果不存在或为空，默认的都为1的，表示无权限
	$Keys=1;  //and U.ModuleId='$ModuleId' 
	$result = mysql_query("SELECT U.Action,U.ModuleId FROM $DataPublic.funmodule F,$DataIn.upopedom U 
							 WHERE U.UserId='$Login_Id'  AND F.KeyWebPage='$KeyWebPage'  
							 and F.ModuleId=U.ModuleId  AND F.Estate>0
							 ORDER BY F.Id LIMIT 1",$link_id);
	if ($myrow = mysql_fetch_array($result,MYSQL_ASSOC)){
		$Keys=$myrow["Action"];
		}
}
echo "<input name='Keys' type='hidden' id='Keys' value='$Keys'>";
?>