<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件图文档";//需处理
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
	$Id=$checkid[$i];
	if ($Id!=""){
		//提取记录
		$checkFile=mysql_fetch_array(mysql_query("SELECT FileName FROM $DataIn.doc_stuffdrawing WHERE Id='$Id' AND Locks=1 LIMIT 1",$link_id));
		$FileName=$checkFile["FileName"];
		if($FileName!=""){
			//删除数据库记录
			$Del = "DELETE FROM $DataIn.doc_stuffdrawing WHERE Id='$Id'"; 
			$result = mysql_query($Del);
			if ($result){
				$Log.="&nbsp;&nbsp;$x-ID号为 $Id 的 $TitleSTR 成功.<br>";
				$y++;
				$FilePath="../download/stuffdrawing/".$FileName;
				if(file_exists($FilePath)){
					unlink($FilePath);
					}
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp;$x-ID号为 $Id 的 $TitleSTR 失败.</div><br>";
				$OperationResult="N";
				}//end if ($result)
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;$x-ID号为 $Id 的 $TitleSTR 失败,记录可能锁定.</div><br>";
			$OperationResult="N";
			}
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>