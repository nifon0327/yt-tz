<?php 
/*$DataIn.电信---yang 20120801
$DataIn.StuffData
$DataIn.cg1_stocksheet
$DataIn.pands
$DataIn.bps
$DataIn.stuffimg
二合一已更新
*/
//步骤1：初始化参数、页面基本信息及CSS、javascrip函数
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件资料";//需处理
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
	$Id=$checkid[$i];//行ID
	if ($Id!=""){
		$sResult = mysql_query("SELECT S.StuffCname,S.StuffId,S.Picture,S.Gfile FROM $DataIn.stuffdata S
				   WHERE S.Id='$Id'  
			        and NOT EXISTS ( SELECT G.StuffId FROM $DataIn.cg1_stocksheet G WHERE G.StuffId=S.StuffId)
			        and NOT EXISTS ( SELECT P.StuffId FROM $DataIn.pands P WHERE P.StuffId=S.StuffId)
			        and NOT EXISTS ( SELECT C.StuffId FROM $DataIn.semifinished_bom C WHERE C.StuffId=S.StuffId OR C.mStuffId=S.StuffId)
			        and NOT EXISTS ( SELECT B.StuffId FROM $DataIn.stuffcombox_bom B WHERE B.StuffId=S.StuffId OR B.mStuffId=S.StuffId)
			        and NOT EXISTS ( SELECT R.StuffId FROM $DataIn.ck1_rksheet R WHERE R.StuffId=S.StuffId)",$link_id);
 		if($sRow = mysql_fetch_array($sResult)){//可删除
			$StuffCname=$sRow["StuffCname"];
			$StuffId=$sRow["StuffId"];
			$Picture=$sRow["Picture"];
			$Gfile=$sRow["Gfile"];
			$DelSql= "DELETE $DataIn.stuffdata,$DataIn.bps,$DataIn.ck9_stocksheet
			FROM $DataIn.stuffdata 
			LEFT JOIN $DataIn.bps ON $DataIn.bps.StuffId=$DataIn.stuffdata.StuffId
			LEFT JOIN $DataIn.ck9_stocksheet ON $DataIn.ck9_stocksheet.StuffId=$DataIn.stuffdata.StuffId
			WHERE $DataIn.stuffdata.StuffId='$StuffId'"; 
			$DelResult = mysql_query($DelSql);
			if($DelResult){
				$Log.=" $x - 配件 $StuffCname / $StuffId 删除成功！<br>";	
				//删除属性
							
				//清除图档
				if($Gfile==1){
					$FilePath="../download/stufffile/g".$StuffId.".jpg";
					if(file_exists($FilePath)){
						unlink($FilePath);
						$Log.="&nbsp;&nbsp;相应的配件图档已清除.<br>";
						}
					}
				//清除图片
				$CheckImgSql=mysql_query("SELECT Picture FROM $DataIn.stuffimg WHERE StuffId='$StuffId' ORDER BY Id",$link_id);
				if($CheckImgRow=mysql_fetch_array($CheckImgSql)){
					do{
						$PictureTemp=$CheckImgRow["Picture"];
						$FilePath="../download/stufffile/".$PictureTemp;
						if(file_exists($FilePath)){
							unlink($FilePath);
							}
						}while($CheckImgRow=mysql_fetch_array($CheckImgSql));
					//删除图片记录
					$DelImgSql="DELETE FROM $DataIn.stuffimg WHERE StuffId='$StuffId'";
					$DelImgResult = mysql_query($DelImgSql);
					
					}
					//删除配件属性
					$DelPropertySql="DELETE FROM $DataIn.stuffproperty  WHERE StuffId='$StuffId'";
					$DelPropertyResult = mysql_query($DelPropertySql);
				}
			else{
				$Log=$Log."<div class='redB'> $x - 删除配件 $StuffId 的操作失败！</div><br>";
				$OperationResult="N";
				}//end if($result0)
			}//end if($sRow = mysql_fetch_array($sResult))
		else{
			$Log.="<div class='redB'>$x - 配件 $StuffId 有使用的历史记录或已设置产品配件关系，不能删除！</div><br>";
			$OperationResult="N";
			}
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&StuffType=$StuffType&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>