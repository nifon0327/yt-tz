<?php
defined('IN_COMMON') || include '../basic/common.php';
//步骤1  $DataIn.sgsdata/ $DataIn.sgsfile 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_".$From;
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="SGS资料";		//需处理
$upDataSheet="$DataIn.sgsdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$FilePath="../download/sgsreport/";
		$uploadNums=count($Picture);
		for($i=0;$i<$uploadNums;$i++){
			//上传文档
			$upPicture=$Picture[$i];
			$TempOldImg=$OldImg[$i];
			if ($upPicture!=""){
				$OldFile=$upPicture;
				//检查是否有原档，如果有则使用原档名称，如果没有，则分配新档名
				/*
				if($TempOldImg!=""){
					$PreFileName=$TempOldImg;
				}
				else{
					$PictureN_name= $_FILES['Picture']['name'][$i];
					$PreFileName=$PictureN_name;
				}
				*/
				$PreFileName="$SgsId"."$i".".jpg";
				$uploadInfo=$PreFileName;
				$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				//$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				if($uploadInfo!=""){
					$SgsFile_Recode="INSERT INTO $DataIn.sgsfile (SgsId,FileName) VALUES ('$SgsId','$PreFileName')";
					$Sgs_Res=@mysql_query($SgsFile_Recode);
					if($Sgs_Res){
						$Log.="SGS扫描档 $i:".$PreFileName.",上传成功,加入数据表成功!<br>";
						}
					else{
						$Log.="SGS扫描档 $i:".$PreFileName.",上传成功,<div class='redB'>加入数据表失败!</div><br>";
						$OperationResult="N";
						}
					}
				else{
					$Log.="SGS扫描档 $i:".$PreFileName.",上传失败,加入数据表失败!<br>";
					$OperationResult="N";
					}
				}
			}

		//重新生成PDF
		$SgsFile_Result = mysql_query("SELECT * from $DataIn.sgsfile WHERE SgsId=$SgsId  order by ID  ",$link_id);
		if ($SgsFile_Row = mysql_fetch_array($SgsFile_Result)) {//有附件
			echo"有附件<br>";
			$filename="../download/sgsreport/".$SgsNo.".pdf";
			if(file_exists($filename)){unlink($filename);}
			define('FPDF_FONTPATH','../plugins/fpdf/font/');
			include "../plugins/fpdf/pdftable.inc.php";		//更新
			$pdf=new PDFTable();
			$pdf->AddGBhwFont('simsun','思源黑体');
			$pdf->AddGBFont('MingLiU','宋繁体');
			$pdf->AddGBFont('Arial','Arial');
			$pdf->Open();
			do{
				$pdf->AddPage();
				$this_Photo=$FilePath.$SgsFile_Row["FileName"];
				$pdf->Image($this_Photo,0,0,210,290,"JPG");
				}while ($SgsFile_Row = mysql_fetch_array($SgsFile_Result));
			$pdf->Output("$filename","F");
			//更新数据
			$SgsFile=$SgsNo.".pdf";
			$PdfFileSTR= ",PdfFile='$SgsFile'";
			}
		else{//没有附件
			$filename="../download/sgsreport/".$SgsNo.".pdf";
			if(file_exists($filename)){unlink($filename);}
			$PdfFileSTR= ",PdfFile=''";
			}

		$ItemE=Chop(trim($ItemE));
		$Sql = "UPDATE $upDataSheet SET SgsNo='$SgsNo',Type='$Type',ItemC='$ItemC',ItemE='$ItemE' $PdfFileSTR  WHERE SgsId=$SgsId";
		$result = mysql_query($Sql);
		if ($result){
			$Log.="&nbsp;&nbsp;&nbsp;&nbsp;SgsId为 $SgsId 的SGS资料更新成功!<br>";
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;SgsId为 $SgsId 的SGS资料更新失败!</div><br>";
			$OperationResult="N";
			}
		$Log_Funtion="SGS资料更新保存";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>