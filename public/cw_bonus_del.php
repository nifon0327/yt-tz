<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="其它奖金记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		//检查是否未结付可删除
		$my_Result = mysql_query("SELECT Bill FROM $DataIn.cw20_bonussheet WHERE Id='$Id' and Estate='1' and Mid='0' ORDER BY Id DESC LIMIT 1",$link_id);
		if($my_Rows=mysql_fetch_array($my_Result)){//可删除
			$Bill=$my_Rows["Bill"];
			$Del = "DELETE FROM $DataIn.cw20_bonussheet WHERE Id='$Id' and Estate=1 and Mid=0"; 
			$result = mysql_query($Del);
			if ($result  && mysql_affected_rows()>0){
				if($Bill==1){
					$Field="C".$Id.".jpg";
					$FileDir="cw_bonus";
					$FilePath="../download/".$FileDir."/".$Field;
					if(file_exists($FilePath)){
						unlink($FilePath);
						}
					}
				$Log.="&nbsp;&nbsp;".$x."-ID号为".$Id."的".$TitleSTR."成功.<br>";
				$y++;
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp;".$x."-ID号为".$Id."的".$TitleSTR."失败.(非未处理状态或其它错误)</div><br>";
				$OperationResult="Y";
				}//end if ($result)
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;".$x."-ID号为".$Id."的".$TitleSTR."失败.(非未处理状态或其它错误)</div><br>";
			$OperationResult="Y";
			}
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
//如果该页记录全删，则返回第一页
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>