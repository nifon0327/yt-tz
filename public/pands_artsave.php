<?php  
//步骤1 $DataIn.stuffdata 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_artsave";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件图档";		//需处理
$upDataSheet="$DataIn.stuffdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$ALType="From=$From&CompanyId=$CompanyId&TypeId=$TypeId&Page=$Page&Pagination=$Pagination";
$FilePath="../download/stufffile/";
//建目录
$upFiles=count($SFcheck);
for ($i=0;$i<$upFiles;$i++){
	$UpdateSTR="";
	$thisSFcheck=$SFcheck[$i];
	$GremarkTemp=$Gremark[$i];
	if($thisSFcheck!=""){//如果选定
		$upPicture=$Picture[$i];
		$oldFileName=$_FILES['Picture']['name'][$i];
		if($upPicture=="" && $GremarkTemp==""){//上传的图档和说明均为空，则清除资料
			$Del_file=$delFile[$i];
			$Gpathfile=$FilePath.$Del_file;
			if(file_exists($Gpathfile)){
				unlink($Gpathfile);
				}
			$UpdateSTR="Gfile='',Gremark='',Gstate='0'";
			}
		else{//更新说明或图档或两者
			if ($upPicture!=""){//如果上传
				$Doc_file=extend_2($oldFileName);
				$OldFile=$upPicture;
				$PreFileName="G".$thisSFcheck.".".$Doc_file;
				$SFN_New=UploadPictures($OldFile,$PreFileName,$FilePath);
				
				$UpdateSTR="Gfile='$SFN_New',Gstate='2'";
				}
			if($GremarkTemp!=""){//更新说明文件
				$UpdateSTR=$UpdateSTR==""?"Gremark='$GremarkTemp'":$UpdateSTR.",Gremark='$GremarkTemp'";
				}
			}
		//图档上传成功，开始更新数据库
		$SF_recodeN="UPDATE $upDataSheet SET $UpdateSTR WHERE StuffId='$thisSFcheck'";
		$SF_resN=@mysql_query($SF_recodeN);
		if($SF_resN){
			$Log.="&nbsp;&nbsp;$i 配件 $thisSFcheck 的图档资料更新成功！ $SF_recodeN </br>";
			}
		else{
			$Log.="&nbsp;&nbsp;$i 配件 $thisSFcheck 的图档资料更新失败！ $SF_recodeN </br>";
			$OperationResult="N";
			}
			
		}//结束选定
	}//end for
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
