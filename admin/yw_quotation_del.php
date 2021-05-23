<?php   
//电信-zxq 2012-08-01
//$DataIn.yw4_quotationsheet 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="Quotation Sheet";//需处理
$Log_Funtion="deleted";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$y=0;
$DirPath="../download/quotation/";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$checkNumber=mysql_fetch_array(mysql_query("SELECT Number FROM $DataIn.yw4_quotationsheet WHERE Id='$Id' LIMIT 1",$link_id));
		$NumberTemp=$checkNumber["Number"];
		//删除数据库记录
		$delSql = "DELETE FROM $DataIn.yw4_quotationsheet WHERE Id='$Id'"; 
		$delRresult = mysql_query($delSql);
		if ($delRresult && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp; $x - 报价单号为 $NumberTemp 的 $Log_Item 删除操作成功.<br>";
			//删除图片
			$Image1Path=$DirPath.$NumberTemp."-01.jpg";
			if(file_exists($Image1Path)){
				unlink($Image1Path);
				}
			$Image2Path=$DirPath.$NumberTemp."-02.jpg";
			if(file_exists($Image2Path)){
				unlink($Image2Path);
				}
			$Image3Path=$DirPath.$NumberTemp."-03.jpg";
			if(file_exists($Image3Path)){
				unlink($Image3Path);
				}
			$PdfPaht=$DirPath.$NumberTemp.".pdf";
			if(file_exists($PdfPath)){
				unlink($PdfPath);
				}
			$y++;
			}
		else{
			$OperationResult="N";
			$Log.="<div class='redB'>$x - &nbsp;&nbsp;报价单号为 $NumberTemp 的 $Log_Item 删除操作失败.</div><br>";
			}//end if ($Del_result)
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.yw4_quotationsheet");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>