<?php 
//$DataIn.sgsdata/ $DataIn.sgsfile 二合一已更新 电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="SGS资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);

$ALType="ComPanyId=$CompanyId";
$FilePath="../download/sgsreport/";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){		
		$FileResult = mysql_query("SELECT D.SgsId,D.PdfFile,F.FileName FROM $DataIn.sgsdata D LEFT JOIN $DataIn.sgsfile F ON F.SgsId=D.SgsId WHERE D.Id=$Id",$link_id);
		if($FileRow = mysql_fetch_array($FileResult)){
			$SgsId=$FileRow["SgsId"];
			$PdfFile=$FileRow["PdfFile"];
			$PdfFilePath=$FilePath.$PdfFile;
			do{//清除sgs图片
				$FileName=$FileRow["FileName"];
				$sgsFilePath=$FilePath.$FileName;
				if(file_exists($sgsFilePath)){
					unlink($sgsFilePath);
					}
				}while($FileRow = mysql_fetch_array($FileResult));
			//清除表记录
			$DelSql = "DELETE $DataIn.sgsfile,$DataIn.sgsdata FROM $DataIn.sgsfile LEFT JOIN $DataIn.sgsdata ON $DataIn.sgsdata.SgsId=$DataIn.sgsfile.SgsId WHERE $DataIn.sgsfile.SgsId='$SgsId'"; 
			
			$DelResult = mysql_query($DelSql);
			if($DelResult){
				//清除PDF文件
				if(file_exists($PdfFilePath)){
					unlink($PdfFilePath);
					}
				$Log.="ID号为 $Id 的SGS资料成功删除.<br>";
				}
			else{
				$Log.="<div class=redB>ID号为 $Id 的SGS资料删除失败.</div><br>";
				$OperationResult="N";
				}//end if ($result)
			}//end if($FileRow = mysql_fetch_array($FileResult))
		else{
			$Log.="<div class=redB>ID号为 $Id 的SGS资料删除失败.</div><br>";
			$OperationResult="N";
			}
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>