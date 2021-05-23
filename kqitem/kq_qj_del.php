<?php 
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="请假记录";//需处理
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
		$OtherWhere="AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month=left($DataPublic.kqqjsheet.StartDate,7) ORDER BY Number)";
		$DelSql = "DELETE FROM $DataPublic.kqqjsheet WHERE Id='$Id' $OtherWhere"; 
		$DelResult = mysql_query($DelSql);
		if($DelResult && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp; $x -ID号为 $Id 的 $TitleSTR 成功!</br>";
			$Proof="../download/bjproof/proof".$Id.".jpg";
			if(file_exists($Proof)){
				unlink($Proof);
				}
			$y++;
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp; $x -ID号为 $Id 的 $TitleSTR 失败!</div></br>";
			$OperationResult="N";
			}
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
$Page=$IdCount==$y?1:$Page;
$chooseMonth=$IdCount==$y?"":$chooseMonth;
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>