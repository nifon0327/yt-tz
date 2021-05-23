<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
//步骤2：
$Log_Item="安全生产负责人培训记录";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//读取员工编号
$Name=FormatSTR($Name);
$checStaffSql=mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE Estate='1' AND cSign='$Login_cSign' AND Name='$Name' ",$link_id);
if($checkStaffRow=mysql_fetch_array($checStaffSql)){
	$Number=$checkStaffRow["Number"];
	if($Attached!=""){//有上传文件
		$FileType=substr("$Attached_name", -4, 4);
		$OldFile=$Attached;
		$FilePath="../download/aqsc/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$datelist=newGetDateSTR();
		$PreFileName="10_".$datelist.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if ($Attached!=""){		
			$Address=FormatSTR($Address);
			$Staff=FormatSTR($Staff);
			$Job=FormatSTR($Job);
			$TrainContent=FormatSTR($TrainContent);
			$IN_recode="INSERT INTO $DataPublic.aqsc10 (Id,TrainDate,Address,TrainContent,Number,Job,Attached,Date,Estate,Locks,Operator) VALUE (NULL,'$TrainDate','$Address','$TrainContent','$Number','$Job','$Attached','$DateTime','1','0','$Operator')";
			$res=@mysql_query($IN_recode);
			if($res){
				$Log="$TitleSTR 成功. <br>";
				}
			else{
				$Log="<div class='redB'>$TitleSTR 失败(或更新无变化).$IN_recode</div><br>";
				$OperationResult="N";
				//删除文件
				}
			}
		else{
			$Log="<div class='redB'>附件上传失败！</div><br>";
			$OperationResult="N";
			//删除附件
			$FilePath="../download/aqsc/".$PreFileName;
			if(file_exists($FilePath)){
				unlink($FilePath);
				}
			}
		}
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败(找不到员工资料). </div><br>";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
