<?php 
//电信-ZX  2012-08-01
/*
$DataIn.productdata
$DataIn.pands
$DataIn.yw1_ordersheet 
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品资料";//需处理
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
		//检查产品资料是否锁定,是否可以删除
			$DelSql = "DELETE FROM $DataIn.newproductdata  where Id='$Id'";
			//echo "$DelSql";
			$DelResult = mysql_query($DelSql);
			if($DelResult){
				$y++;
				$Log.="$x - 产品 $cName /$ProductId 删除成功。<br>";
				if ($TestStandard==1){
					$delFile="T".$ProductId.".jpg";
					$FilePath="../download/newproductdata/".$delFile;
					if(file_exists($FilePath)){unlink($FilePath);}
					}
				}
			else{
				$Log.="<div class='redB'>$x - 产品 $cName /$ProductId 删除失败。</div><br>";
				$OperationResult="N";
				}//if($Del_Result)
		$x++;
		}//end if($Id!="")
	}//end for 
//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&CompanyId=$CompanyId&TypeId=$TypeId&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>