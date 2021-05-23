<?php 

include "../basic/downloadFileIP.php";  //取得下载文档的IP
if ($donwloadFileIP=="") {
	$donwloadFileIP="..";    //无IP，则用原来的方式
	$donwloadFileaddress="$donwloadFileIP/admin/openorload.php";
}

//echo "$donwloadFileIP";
//$DataIn.stuffimg 分开已更新电信---yang 20120801
$arrays=""; //传递参数
$style="";
if ($ComeFrom=="Supplier"){  //如果来自供应商，则强行显示只是状态为1有
  //if(($Gstate!=1) && ($Gstate!=7)){
   //if($Gstate==1){	  // modify by zx 2011-04-15 供应商只有1的才可以下载
   switch ($Gstate){
	 case 1:  
		$Operator=$Login_P_Number;
		$arrays="Supplier|$StuffId|$myCompanyId|$Operator|$Login_IP";    // $Login_IP add by zx 2012-1214
		$checkCaseSql=mysql_query("SELECT CompanyId FROM $DataIn.stuffprovider  WHERE StuffId='$StuffId' AND  CompanyId='$myCompanyId' AND Estate=1 LIMIT 1",$link_id);  //存在，说明已点击过
			if($checkCaseRow=mysql_fetch_array($checkCaseSql)){
				$GfileDate=$GfileDate;
			}
			else
			{
				if( is_null($GfileDate) ||  !isset($GfileDate) || ($GfileDate=="") || ($GfileDate=='&nbsp;')){ 
					$style="";
				}
				else {
					$Gremark="已更新图档 :$GfileDate";
					$GfileDate="<div class='redB'>$GfileDate</div>";
					$style="background:#F00";
					
				}
				
			}
		break;
	case 2:  
	case 4:  	
	case 6:  	
	case 7: 
	    //$GfileDate="&nbsp;";
	    $Gstate=-2; 
		break;
    default :
		$Gstate=-1; 
		break;	
  }
}
//echo "Gfile:$Gfile";
//echo "Gstate:$Gstate";
if($Gfile!=""){
	$Gfile=anmaIn($Gfile,$SinkOrder,$motherSTR);
	switch ($Gstate){
		case -2:
			$Gfile="<div class='redB'>处理中</div>";
			if( is_null($GfileDate) ||  !isset($GfileDate) || ($GfileDate=="") || ($GfileDate=='&nbsp;'))
			{ //
			}
			else {
				$GfileDate="<div class='redB'>$GfileDate</div>";
			}
			break;
		case -1: $Gfile="&nbsp;"; break;
		case 2:
			$Gfile="<a href=\"$donwloadFileaddress?d=$d&f=$Gfile&Type=&Action=6\" target=\"download\" ><img src='../images/down.gif' style='background:#F00' alt='图档未审核' width='18' height='18'></a>";//显示红色	
			break;
		case 4:
			$Gfile="<a href=\"$donwloadFileaddress?d=$d&f=$Gfile&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='background:#FFFF00' alt='请重新上传图档' width='18' height='18'></a>";//显示红色
			break;
		case 6:
			$tmpd=anmaIn("download/stufffile_tmp/",$SinkOrder,$motherSTR);		
			$Gfile="<a href=\"$donwloadFileaddress?d=$tmpd&f=$Gfile&Type=&Action=6\" target=\"download\" ><img src='../images/down.gif' style='background:#F00' alt='图档未审核S' width='18' height='18'></a>";
			break;
	    case 7:  //	PDF		
			$Gfile="<a href=\"$donwloadFileaddress?d=$d&f=$Gfile&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='background:#0033FF' alt='请重新上传图档' width='18' height='18'></a>";//显示红色
		    break;	
		default:
			$Gfile="<a href=\"$donwloadFileaddress?d=$d&f=$Gfile&Type=&Action=6&arrays=$arrays\" target=\"download\"><img src='../images/down.gif' style='$style' alt='$Gremark' width='18' height='18' style='border:0'></a>";

			break;		
	}
}
else{
	$Gfile="&nbsp;";
	}
	
	
	
/*
//检查是否存在条码或标签文件:需要有记录且文件存在
$CodeFile="&nbsp;";
$LableFile="&nbsp;";
$BoxFile="&nbsp;";
$checkCodeFileSql=mysql_query("SELECT F.CodeType,F.Estate FROM $DataIn.stuffcodefile F
WHERE F.StuffId='$StuffId' ORDER BY F.CodeType",$link_id);
if($checkCodeFileRow=mysql_fetch_array($checkCodeFileSql)){
	//检查文件是否存在
	do{
		$CodeType=$checkCodeFileRow["CodeType"];
		$CFile=$StuffId."-".$CodeType.".qdf";
		$CodeFileFilePath="../download/stufffile/".$CFile;
		if(file_exists($CodeFileFilePath)){
			
			switch($CodeType){
				case 1:$TypeSTR="背卡条码";break;
				case 2:$TypeSTR="白盒标签";break;
				case 3:$TypeSTR="外箱标签";break;
				}
			$CodeFileEstate=$checkCodeFileRow["Estate"];
			switch($CodeFileEstate){
				case 1://审核不通过
					$AltStr=$TypeSTR."文件审核未通过,请重新上传.";
					if($CodeType==1){
						$CodeFile="<img src='../images/del.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==2){
						$LableFile="<img src='../images/del.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==3){
						$BoxFile="<img src='../images/del.gif' alt='$AltStr' width='18' height='18'>";
						}
					break;
				case 2://审核中
					$AltStr=$TypeSTR."文件审核中";
					if($CodeType==1){
						$CodeFile="<img src='../images/audit.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==2){
						$LableFile="<img src='../images/audit.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==3){
						$BoxFile="<img src='../images/audit.gif' alt='$AltStr' width='18' height='18'>";
						}
					break;
				case 0://审核通过
					$AltStr=$TypeSTR."文件,用于车间自行打印";
					$CFile=anmaIn($CFile,$SinkOrder,$motherSTR);
					if($CodeType==1){
						$CodeFile="<img onClick='OpenOrLoad(\"$d\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==2){
						$LableFile="<img onClick='OpenOrLoad(\"$d\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						}
					if($CodeType==3){
						$BoxFile="<img onClick='OpenOrLoad(\"$d\",\"$CFile\",6)' src='../images/down.gif' alt='$AltStr' width='18' height='18'>";
						}
					break;
				}
			}
		}while($checkCodeFileRow=mysql_fetch_array($checkCodeFileSql));
	}
*/	
?>