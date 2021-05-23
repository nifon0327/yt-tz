<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="安全生产负责人培训记录";		//需处理
$upDataSheet="$DataPublic.aqsc10";	//需处理
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
				if($oldAttached!=""){
					$PreFileName=$oldAttached;
					}
				else{
					$datelist=newGetDateSTR();
					$PreFileName="10_".$datelist.$FileType;
					}
				$upAttached=UploadPictures($OldFile,$PreFileName,$FilePath);
				if($upAttached!=""){
					$AttachedSTR=",Attached='$PreFileName'";
					}
				}
			$Address=FormatSTR($Address);
			$Job=FormatSTR($Job);
			$TrainContent=FormatSTR($TrainContent);
			$SetStr="TrainDate='$TrainDate',Address='$Address',TrainContent='$TrainContent',Number='$Number',Job='$Job',Date='$DateTime',Operator='$Operator',Locks='0' $AttachedSTR";
			include "../model/subprogram/updated_model_3a.php";
			}
		else{
			$Log="<div class='redB'>$TitleSTR 失败(找不到员工资料). </div><br>";
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>