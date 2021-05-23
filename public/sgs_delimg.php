<?php
defined('IN_COMMON') || include '../basic/common.php';
//步骤1：$DataShare.sgsfile 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$FilePath="../download/sgsreport/";
$ChechSql=mysql_query("SELECT F.SgsId,F.FileName,D.SgsNo FROM $DataIn.sgsfile F,$DataIn.sgsdata D WHERE F.Id=$ImgId AND F.SgsId=D.SgsId",$link_id);
if($CheckRow=mysql_fetch_array($ChechSql)){echo $ImgId;
	$FileName=$CheckRow["FileName"];
	$SgsId=$CheckRow["SgsId"];
	$SgsNo=$CheckRow["SgsNo"];
	$delSql="DELETE FROM $DataIn.sgsfile WHERE Id=$ImgId";
	$result1 = mysql_query($delSql);
	if($result1){
		$ImgName=$FilePath.$FileName;
		if(file_exists($ImgName)){unlink($ImgName);echo"图片删除成功";}
		//PDF重新生成
		$PdfName=$FilePath.$SgsNo.".pdf";
		if(file_exists($PdfName)){unlink($PdfName);echo"pdf删除成功";}
		define('FPDF_FONTPATH','../plugins/fpdf/font/');
		include "../plugins/fpdf/pdftable.inc.php";		//更新
		$pdf=new PDFTable();
		$pdf->AddGBhwFont('simsun','思源黑体');
		$pdf->AddGBFont('MingLiU','宋繁体');
		$pdf->AddGBFont('Arial','Arial');
		$pdf->Open();
		$SgsFile_Result = mysql_query("SELECT * FROM $DataIn.sgsfile WHERE SgsId='$SgsId' ORDER BY Id",$link_id);
		if($SgsFile_Row = mysql_fetch_array($SgsFile_Result)){
			do{
				$pdf->AddPage();
				echo "a <br>";
				$this_Photo=$FilePath.$SgsFile_Row["FileName"];
				$pdf->Image($this_Photo,0,0,210,290,"JPG");
				}while ($SgsFile_Row = mysql_fetch_array($SgsFile_Result));
			$pdf->Output("$PdfName","F");
			//更新数据
			$SgsFile=$SgsNo.".pdf";
			$Update_Sql = "UPDATE $DataIn.sgsdata SET PdfFile='$SgsFile' WHERE SgsId=$SgsId";
			$Update_Result = mysql_query($Update_Sql);
			if($Update_Result){
				$Log.="此SGS的PDF文档创建成功!<br>";
				}
			else{
				$Log.="此SGS的PDF文档创建失败!<br>";
				$OperationResult="N";
				}
			}//if($SgsFile_Row = mysql_fetch_array($SgsFile_Result))
		}//if($result1)
	}
echo $Log;
?>