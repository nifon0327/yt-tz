<?php 
//$DataIn.cwxzsheet 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工薪资记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
for($i=0;$i<count($checkid);$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//删除数据库记录;条件当月记录没结付
		$checkRows=mysql_fetch_array(mysql_query("SELECT Month,Number FROM $DataIn.cwxzsheet WHERE Id='$Id'",$link_id));
		$C_Number=$checkRows["Number"];
		$C_Month=$checkRows["Month"];
		
		$Del = "DELETE FROM $DataIn.cwxzsheet WHERE Id='$Id' AND Estate='1'"; 
		$result = mysql_query($Del);
		if($result && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp; $x -ID号为 $Id 的".$TitleSTR."成功!</br>";
			//相关项目处理:奖惩、其它、借支
			//借支数据连接主ID
			$upResult1= mysql_query("UPDATE $DataIn.cwygjz SET Mid='0',Locks='1' WHERE Mid='$Mid'",$link_id);
			//$upResult1 = mysql_query($upSql1);
			//奖惩数据连接主ID
			$upResult2 = mysql_query("UPDATE $DataIn.staffrandp SET Mid='0',Locks='1' WHERE Mid='$Mid'",$link_id);
			//$upResult2 = mysql_query($upSql2);
			
			$Del2 = "DELETE FROM $DataIn.hdjbsheet WHERE Month='$C_Month' and Number='$C_Number' "; 
		    $result2 = mysql_query($Del2);
		    
			$y++;
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp; $x -ID号为 $Id 的".$TitleSTR."失败!</div></br>";
			$OperationResult="N";
			}
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cwxzsheet");
$Page=$IdCount==$y?1:$Page;
$Estate=$IdCount==$y?"":$Estate;
$chooseMonth=$IdCount==$y?"":$chooseMonth;
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>