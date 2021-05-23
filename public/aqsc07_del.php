<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="安全生产培训计划";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作：没有使用记录时可删除？
$x=1;$y=0;
for($i=0;$i<count($checkid);$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//读取附件
		$Result = mysql_query("SELECT ItemName FROM $DataPublic.aqsc07 WHERE Id='$Id' ORDER BY Id DESC LIMIT 1",$link_id);
		if ($myrow = mysql_fetch_array($Result)) {
			$ItemName=$myrow["ItemName"];
			//删除数据库记录
			$Del = "DELETE FROM $DataPublic.aqsc07 WHERE Id='$Id'"; 
			$result = mysql_query($Del);
			if ($result && mysql_affected_rows()>0){
				//清除文件
				$imgPath="../download/aqsc/aqsc07_img_".$Id.".pdf";
				if(file_exists($imgPath)){
					unlink($imgPath);
					}
				$MoviePath="../download/aqsc/aqsc07_movie_".$Id.".pdf";
				if(file_exists($MoviePath)){
					unlink($MoviePath);
					}
				$ListPath="../download/aqsc/aqsc07_list_".$Id.".pdf";
				if(file_exists($ListPath)){
					unlink($ListPath);
					}
				$Log.="&nbsp;&nbsp; $x -计划为".$ItemName."的".$TitleSTR."成功.<br>";
				$y++;
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp; $x -计划为".$ItemName."的".$TitleSTR."失败.</div><br>";
				}//end if ($result)
			}// end if ($myrow = mysql_fetch_array($Result))
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>