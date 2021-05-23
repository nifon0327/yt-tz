<?php 
//电信-ZX  2012-08-01
//$DataIn.bulletin 二合一已更新 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="公告";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//读取附件
		$Result = mysql_query("SELECT Caption,Attached FROM $DataIn.bulletin WHERE Id='$Id' ORDER BY Id DESC",$link_id);
		if ($myrow = mysql_fetch_array($Result)) {
			$Caption=$myrow["Caption"];
			$Attached=$myrow["Attached"];
			//删除数据库记录
			$Del = "DELETE FROM $DataIn.bulletin WHERE Id='$Id'"; 
			$result = mysql_query($Del);
			if ($result && mysql_affected_rows()>0){
				if($Attached!=""){//删除附件
					$FilePath="../download/bulletinattached/".$Attached;
					if(file_exists($FilePath)){
						unlink($FilePath);
						}
					}
				$Log.="&nbsp;&nbsp; $x -标题为".$Caption."的".$TitleSTR."成功.<br>";
				$y++;
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp; $x -标题为".$Caption."的".$TitleSTR."失败.</div><br>";
				}//end if ($result)
			}// end if ($myrow = mysql_fetch_array($Result))
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.bulletin");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>