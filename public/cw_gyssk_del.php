<?php 
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
//步骤1：初始化参数、页面基本信息及CSS、javascrip函数
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="供应商税款";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=1;
$FileDir="cwgyssk";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		//检查是否未结付可删除
		$my_Result = mysql_query("SELECT InvoiceFile FROM $DataIn.cw2_gyssksheet WHERE Id='$Id' and Estate='1' and Mid='0' ORDER BY Id DESC LIMIT 1",$link_id);
		if($my_Rows=mysql_fetch_array($my_Result)){//可删除
			$InvoiceFile=$my_Rows["InvoiceFile"];
			$Del = "DELETE FROM $DataIn.cw2_gyssksheet WHERE Id='$Id' and Estate=1 and Mid=0"; 
			$result = mysql_query($Del);
			if ($result  && mysql_affected_rows()>0){
				if($InvoiceFile==1){
					$Field="S".$Id.".jpg";
					$FilePath="../download/".$FileDir."/".$Field;
					if(file_exists($FilePath)){
						unlink($FilePath);
						}
					}
				$addRecodes="DELETE FROM $DataIn.cw2_gysskrelation WHERE Mid='$Id' ";
				//echo "addRecodes:$addRecodes";
				$addAction=@mysql_query($addRecodes);
				
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
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw2_gyssksheet");
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>