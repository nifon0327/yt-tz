<?php
defined('IN_COMMON') || include '../basic/common.php';
//$DataIn.sgsfile /$DataIn.sgsdata 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="SGS资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&CompanyId=$CompanyId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");

//计算SGS测试报告ID
//$LockSql=" LOCK TABLES $DataIn.sgsdata WRITE";$LockRes=@mysql_query($LockSql);
$Sgs_Temp=mysql_query("SELECT MAX(SgsId) AS abc FROM $DataIn.sgsdata",$link_id);
$Id_temp=mysql_result($Sgs_Temp,0,"abc");
if($Id_temp){
	$SgsId=$Id_temp+1;}
else{
	$SgsId=70001;
	}
//解锁表
//$unLockSql="UNLOCK TABLES";	$unLockRes=@mysql_query($unLockSql);

$ItemE=Chop(trim($ItemE));
$Type=Chop(trim($Type));
$IN_recode="INSERT INTO $DataIn.sgsdata (Id,CompanyId,SgsId,SgsNo,Type,ItemC,ItemE,PdfFile,Locks,Date,Operator) VALUES (NULL,'$CompanyId','$SgsId','$SgsNo','$Type','$ItemC','$ItemE','','0','$Date','$Operator')";
$res=@mysql_query($IN_recode);
if ($res){
	$Log="SGS测试报告添加成功!<br>";
 	}
else{
	$Log="<div class='redB'>SGS测试报告添加失败! $IN_recode </div><br>";
	$OperationResult="N";
	}
if($OperationResult=="Y"){
	//上传图片
	$FilePath="../download/sgsreport/";
	$uploadNums=count($Picture);
	for($i=0;$i<$uploadNums;$i++){
		//上传文档
		$upPicture=$Picture[$i];

		if ($upPicture!=""){
			$PictureN_name= $_FILES['Picture']['name'][$i];
			$OldFile=$upPicture;
			$PreFileName=$PictureN_name;
			$FileType = strtolower(substr($PreFileName, strrpos($PreFileName, '.') + 1));
			$NewFileName=$SgsNo.".pdf";
			$uploadInfo=UploadFiles($OldFile,$NewFileName,$FilePath);
			if($uploadInfo!=""){
				//写入记录
				if($FileType=="pdf")
				{
					$upRecode="UPDATE $DataIn.sgsdata SET PdfFile='$NewFileName' WHERE SgsId=$SgsId";
					$upAction=@mysql_query($upRecode);

					$inRecode="INSERT INTO $DataIn.sgsfile (Id,FileName,SgsId) VALUES (NULL,'','$SgsId')";
					$inAction=@mysql_query($inRecode);
					if($inAction && $upAction){
						$Log.="SGS扫描档 $i:".$PreFileName.",上传成功,加入数据表成功!<br>";
						}
					else{
						$Log.="SGS扫描档 $i:".$PreFileName.",上传成功,<div class='redB'>加入数据表失败! $inRecode </div><br>";
						$OperationResult="N";
						}
				}else
				{
					$inRecode="INSERT INTO $DataIn.sgsfile (Id,FileName,SgsId) VALUES (NULL,'$uploadInfo','$SgsId')";
					$inAction=@mysql_query($inRecode);
					if($inAction && $upAction){
						$Log.="SGS扫描档 $i:".$PreFileName.",上传成功,加入数据表成功!<br>";
						}
					else{
						$Log.="SGS扫描档 $i:".$PreFileName.",上传成功,<div class='redB'>加入数据表失败! $inRecode </div><br>";
						$OperationResult="N";
					}
				}

				}
			}
		}
	//如果上传的是jpg,则生成PDF
	//检查是否已建立文档,如果是,则先删除后创建
	if($FileType=="jpg"){
	$filename=$FilePath.$SgsNo.".pdf";
	if(file_exists($filename)){
		unlink($filename);
		}
	define('FPDF_FONTPATH','../plugins/fpdf/font/');
	include "../plugins/fpdf/pdftable.inc.php";		//更新
	$pdf=new PDFTable();
	$pdf->AddGBhwFont('simsun','思源黑体');
	$pdf->AddGBFont('MingLiU','宋繁体');
	$pdf->AddGBFont('Arial','Arial');
	$pdf->Open();

	$SgsFile_Result = mysql_query("SELECT * FROM $DataIn.sgsfile WHERE SgsId='$SgsId' ORDER BY Id",$link_id);
	if($SgsFile_Row = mysql_fetch_array($SgsFile_Result)) {
		do{
			$pdf->AddPage();
			$this_Photo=$FilePath.$SgsFile_Row["FileName"];
			$pdf->Image($this_Photo,0,0,210,290,"JPG");
			}while ($SgsFile_Row = mysql_fetch_array($SgsFile_Result));
		$pdf->Output("$filename","F");
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
		}
	}
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>