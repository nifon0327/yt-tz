<?php 
//电信-zxq 2012-08-01
//$DataIn.cwygjz 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="借支记录";//需处理
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
		//删除数据库记录
		$Del = "DELETE FROM $DataIn.cwygjz WHERE Id='$Id' and Mid='0' and InDate='0000-00-00'"; 
		$result = mysql_query($Del);
		if ($result && mysql_affected_rows()>0){
			$FilePath="../download/cwygjz/J".$Id.".jpg";
			if(file_exists($FilePath)){
				unlink($FilePath);
				}
			$Log.="&nbsp;&nbsp; $x -Id为".$Id."的".$TitleSTR."成功.<br>";
			$y++;
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp; $x -Id为".$Id."的".$TitleSTR."失败.</div> $Del<br>";
			$OperationResult="N";
			}//end if ($result)
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cwygjz");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>